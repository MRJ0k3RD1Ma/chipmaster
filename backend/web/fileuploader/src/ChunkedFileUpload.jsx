import { useState, useRef } from 'react'

const CHUNK_SIZE = 1024 * 1024 // 1MB chunks

function ChunkedFileUpload({ token }) {
  const [file, setFile] = useState(null)
  const [progress, setProgress] = useState(0)
  const [uploading, setUploading] = useState(false)
  const [error, setError] = useState(null)
  const [success, setSuccess] = useState(false)
  const abortControllerRef = useRef(null)
  const fileIdRef = useRef(null)

  const handleFileChange = (e) => {
    const selectedFile = e.target.files[0]
    if (selectedFile) {
      setFile(selectedFile)
      setProgress(0)
      setError(null)
      setSuccess(false)
    }
  }

  const uploadChunk = async (chunk, chunkIndex, totalChunks, fileId, fileName) => {
    const formData = new FormData()
    formData.append('chunk', chunk)
    formData.append('chunkIndex', chunkIndex)
    formData.append('totalChunks', totalChunks)
    formData.append('fileId', fileId)
    formData.append('fileName', fileName)

    const response = await fetch('http://chip.lc/api/v1/file/upload', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
      },
      body: formData,
      signal: abortControllerRef.current?.signal,
    })

    const data = await response.json()

    if (!response.ok) {
      throw new Error(data.message || `Chunk ${chunkIndex + 1} yuklashda xatolik`)
    }

    return data
  }

  const handleUpload = async () => {
    if (!file) {
      setError('Iltimos, fayl tanlang')
      return
    }

    if (!token) {
      setError('Iltimos, token kiriting')
      return
    }

    setUploading(true)
    setError(null)
    setSuccess(false)
    setProgress(0)

    abortControllerRef.current = new AbortController()

    const totalChunks = Math.ceil(file.size / CHUNK_SIZE)
    const fileId = crypto.randomUUID()
    fileIdRef.current = fileId

    try {
      let result = null

      for (let i = 0; i < totalChunks; i++) {
        const start = i * CHUNK_SIZE
        const end = Math.min(start + CHUNK_SIZE, file.size)
        const chunk = file.slice(start, end)

        result = await uploadChunk(chunk, i, totalChunks, fileId, file.name)

        const newProgress = Math.round(((i + 1) / totalChunks) * 100)
        setProgress(newProgress)
      }

      setSuccess(true)
      console.log('Yuklandi:', result)
    } catch (err) {
      if (err.name !== 'AbortError') {
        setError(err.message || 'Yuklashda xatolik yuz berdi')
      }
    } finally {
      setUploading(false)
      abortControllerRef.current = null
    }
  }

  const handleCancel = async () => {
    if (abortControllerRef.current) {
      abortControllerRef.current.abort()

      // Server dagi temp fayllarni tozalash
      if (fileIdRef.current) {
        try {
          await fetch('http://chip.lc/api/v1/file/cancel', {
            method: 'POST',
            headers: {
              'Authorization': `Bearer ${token}`,
              'Content-Type': 'application/json',
            },
            body: JSON.stringify({ fileId: fileIdRef.current }),
          })
        } catch (e) {
          console.error('Temp fayllarni tozalashda xato:', e)
        }
      }

      setUploading(false)
      setProgress(0)
      setError('Yuklash bekor qilindi')
      fileIdRef.current = null
    }
  }

  return (
    <div>
      <h2>Ketma-ket Yuklash</h2>
      <p style={{ color: '#666', fontSize: '14px' }}>
        Fayllar bo'laklarga bo'linib ketma-ket yuboriladi
      </p>

      <div style={{ marginBottom: '15px' }}>
        <input
          type="file"
          onChange={handleFileChange}
          disabled={uploading}
          style={{ marginBottom: '10px' }}
        />
        {file && (
          <p style={{ margin: '5px 0', fontSize: '14px' }}>
            Tanlangan fayl: {file.name} ({(file.size / (1024 * 1024)).toFixed(2)} MB)
          </p>
        )}
      </div>

      {progress > 0 && (
        <div style={{ marginBottom: '15px' }}>
          <div
            style={{
              width: '100%',
              height: '20px',
              backgroundColor: '#e0e0e0',
              borderRadius: '10px',
              overflow: 'hidden',
            }}
          >
            <div
              style={{
                width: `${progress}%`,
                height: '100%',
                backgroundColor: success ? '#4caf50' : '#2196f3',
                transition: 'width 0.3s ease',
              }}
            />
          </div>
          <p style={{ textAlign: 'center', marginTop: '5px' }}>{progress}%</p>
        </div>
      )}

      {error && (
        <p style={{ color: '#f44336', marginBottom: '15px' }}>{error}</p>
      )}

      {success && (
        <p style={{ color: '#4caf50', marginBottom: '15px' }}>
          Fayl muvaffaqiyatli yuklandi!
        </p>
      )}

      <div>
        {!uploading ? (
          <button
            onClick={handleUpload}
            disabled={!file || !token}
            style={{
              padding: '10px 20px',
              fontSize: '16px',
              backgroundColor: '#2196f3',
              color: 'white',
              border: 'none',
              borderRadius: '5px',
              cursor: file && token ? 'pointer' : 'not-allowed',
              opacity: file && token ? 1 : 0.6,
            }}
          >
            Yuklash
          </button>
        ) : (
          <button
            onClick={handleCancel}
            style={{
              padding: '10px 20px',
              fontSize: '16px',
              backgroundColor: '#f44336',
              color: 'white',
              border: 'none',
              borderRadius: '5px',
              cursor: 'pointer',
            }}
          >
            Bekor qilish
          </button>
        )}
      </div>
    </div>
  )
}

export default ChunkedFileUpload
