<!DOCTYPE html>
<html>
<head>
    <title>Settings - API Key Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        use App\Models\Setting;
    @endphp
    <link rel="stylesheet" href="{{ asset('css/settings.css') }}">
</head>
<body>
    <div class="container">
        <a href="/" class="back-link">← Back to Chat</a>
        
        <h1>Settings Management</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        
        <div class="card card-gradient mb-20">
            <h2>Add Your Free API Keys</h2>
            <p>Groq supports unlimited use. Add as many keys as you like (GROQ_API_KEY, GROQ_API_KEY_2, GROQ_API_KEY_3, ...); rotation will try each in order.</p>
            
            <div class="key-grid">
                <button class="btn btn-white" 
                    onclick="quickAdd('GROQ_API_KEY', 'Your Groq API Key', 'Get free unlimited key at https://console.groq.com')">
                    Groq Key #1
                </button>
                <button class="btn btn-white" 
                    onclick="quickAdd('GROQ_API_KEY_2', 'Another Groq API Key', 'Optional backup key #2 from https://console.groq.com')">
                    Groq Key #2
                </button>
                <button class="btn btn-white" 
                    onclick="quickAdd('GROQ_API_KEY_3', 'Another Groq API Key', 'Optional backup key #3 from https://console.groq.com')">
                    Groq Key #3
                </button>
                <button class="btn btn-white" 
                    onclick="quickAddDynamicGroq()">
                    Add Next Groq Key
                </button>
            </div>
        </div>

        <div class="card">
            <h2 class="mb-20">API Keys & Configuration</h2>
            
            <table class="settings-table">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($settings as $setting)
                        <tr class="{{ $setting->key == 'AI_PROVIDER' ? 'hidden' : '' }}">
                            <td><strong>{{ $setting->key }}</strong></td>
                            <td>
                                @if($setting->key == 'AI_PROVIDER')
                                    <span class="provider-badge">
                                        {{ strtoupper($setting->value) }}
                                    </span>
                                @else
                                    <code class="value-code">
                                        {{ Str::mask($setting->value, '*', 10) }}
                                    </code>
                                @endif
                            </td>
                            <td>{{ $setting->description ?? '-' }}</td>
                            <td>
                                <div class="actions">
                                    <button class="btn btn-edit" onclick="editSetting({{ $setting->id }}, '{{ $setting->key }}', '{{ $setting->value }}', '{{ $setting->description }}')">
                                        Edit
                                    </button>
                                    @if(str_starts_with($setting->key, 'GROQ_API_KEY'))
                                        <button class="btn btn-secondary btn-small" onclick="verifyGroqKey({{ $setting->id }})">Verify</button>
                                    @endif
                                    <form method="POST" action="/settings/{{ $setting->id }}" class="inline-form" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-small">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No settings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <button class="btn btn-primary mt-20" onclick="showAddModal()">+ Add New Setting</button>
        </div>
    </div>

    
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-modal" onclick="closeModal()">&times;</span>
                <h2>Edit Setting</h2>
            </div>
            <form method="POST" action="/settings/update">
                @csrf
                <input type="hidden" name="id" id="edit-id">
                
                <div class="form-group">
                    <label>Key</label>
                    <input type="text" name="key" id="edit-key" readonly>
                </div>

                <div class="form-group">
                    <label>Value</label>
                    <input type="text" name="value" id="edit-value" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit-description"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Setting</button>
            </form>
        </div>
    </div>

    
    <div id="addModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close-modal" onclick="closeAddModal()">&times;</span>
                <h2>Add New Setting</h2>
            </div>
            <form method="POST" action="/settings">
                @csrf
                
                <div class="form-group">
                    <label>Key</label>
                    <input type="text" name="key" placeholder="e.g., OPENAI_API_KEY" required>
                </div>

                <div class="form-group">
                    <label>Value</label>
                    <input type="text" name="value" placeholder="Enter value" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Optional description"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Add Setting</button>
            </form>
        </div>
    </div>

    <script>
        window.existingGroqKeys = @json($settings->where('key', 'LIKE', 'GROQ_API_KEY%')->pluck('key')->values());
    </script>
    <script src="{{ asset('js/settings.js') }}"></script>
</body>
</html>
