<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors Table Data</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .author-card { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .author-header { display: flex; align-items: center; margin-bottom: 15px; }
        .author-avatar { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 24px; margin-right: 15px; }
        .author-info h3 { margin: 0; color: #333; }
        .author-info p { margin: 5px 0; color: #666; }
        .author-details { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .detail-item { background: #f8f9fa; padding: 10px; border-radius: 4px; }
        .detail-label { font-weight: bold; color: #555; display: block; margin-bottom: 5px; }
        .detail-value { color: #333; }
        .status-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-active { background: #d4edda; color: #155724; }
        .success-message { background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .table-info { background: #cce5ff; color: #004085; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Data Authors dari Tabel MySQL</h1>
            <p>Data berhasil disimpan ke tabel <strong>authors</strong> di database MySQL</p>
        </div>

        <div class="success-message">
            ✅ <strong>Berhasil!</strong> Data authors telah berhasil disimpan ke tabel <code>authors</code> di database MySQL
        </div>

        <div class="table-info">
            <strong>📊 Informasi Database:</strong><br>
            - Tabel: <code>authors</code><br>
            - Total Records: {{ count($authors) }}<br>
            - Model: <code>App\Models\Author</code><br>
            - Primary Key: <code>id</code> (string)
        </div>

        @foreach($authors as $author)
        <div class="author-card">
            <div class="author-header">
                <div class="author-avatar">
                    {{ strtoupper(substr($author->name, 0, 1)) }}
                </div>
                <div class="author-info">
                    <h3>{{ $author->name }}</h3>
                    <p><strong>ID:</strong> {{ $author->id }}</p>
                    <p><strong>Email:</strong> {{ $author->email }}</p>
                    <span class="status-badge status-{{ $author->status }}">{{ $author->status }}</span>
                </div>
            </div>

            <div class="author-details">
                <div class="detail-item">
                    <span class="detail-label">📱 Phone</span>
                    <span class="detail-value">{{ $author->phone ?? 'Not provided' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">📍 Address</span>
                    <span class="detail-value">{{ $author->address ?? 'Not provided' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">📝 Bio</span>
                    <span class="detail-value">{{ $author->bio ?? 'Not provided' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">🎯 Specialization</span>
                    <span class="detail-value">{{ $author->specialization ?? 'Not specified' }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">📄 Total Posts</span>
                    <span class="detail-value">{{ $author->total_posts }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">📅 Created At</span>
                    <span class="detail-value">{{ $author->created_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>
        @endforeach

        <div style="margin-top: 30px; padding: 20px; background: #fff; border-radius: 8px;">
            <h3>🔐 Login Credentials:</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4>Author 1:</h4>
                    <p><strong>Email:</strong> author1@gmail.com</p>
                    <p><strong>Password:</strong> author123</p>
                </div>
                <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                    <h4>Author 2:</h4>
                    <p><strong>Email:</strong> sarah.writer@gmail.com</p>
                    <p><strong>Password:</strong> sarah456</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>