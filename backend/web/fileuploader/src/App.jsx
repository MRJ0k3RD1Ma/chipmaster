import { useState } from 'react'
import ChunkedFileUpload from './ChunkedFileUpload'
import ChunkedFileUploadParallel from './ChunkedFileUploadParallel'
import './App.css'

function App() {
  const [token, setToken] = useState('')
  const [uploadType, setUploadType] = useState('sequential')

  return (
    <div style={{ padding: '20px', maxWidth: '600px', margin: '0 auto' }}>
      <h1>Fayl Yuklash Tizimi</h1>

      <div style={{ marginBottom: '20px' }}>
        <label>
          <strong>Bearer Token:</strong>
          <input
            type="text"
            value={token}
            onChange={(e) => setToken(e.target.value)}
            placeholder="Tokeningizni kiriting..."
            style={{
              width: '100%',
              padding: '10px',
              marginTop: '5px',
              fontSize: '14px',
            }}
          />
        </label>
      </div>

      <div style={{ marginBottom: '20px' }}>
        <strong>Yuklash turi:</strong>
        <div style={{ marginTop: '10px' }}>
          <label style={{ marginRight: '20px' }}>
            <input
              type="radio"
              value="sequential"
              checked={uploadType === 'sequential'}
              onChange={(e) => setUploadType(e.target.value)}
            />
            {' '}Ketma-ket yuklash
          </label>
          <label>
            <input
              type="radio"
              value="parallel"
              checked={uploadType === 'parallel'}
              onChange={(e) => setUploadType(e.target.value)}
            />
            {' '}Parallel yuklash (tezroq)
          </label>
        </div>
      </div>

      <hr style={{ margin: '20px 0' }} />

      {uploadType === 'sequential' ? (
        <ChunkedFileUpload token={token} />
      ) : (
        <ChunkedFileUploadParallel token={token} />
      )}
    </div>
  )
}

export default App
