function editSetting(id, key, value, description) 
{
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-key').value = key;
    document.getElementById('edit-value').value = value;
    document.getElementById('edit-description').value = description;
    document.getElementById('editModal').classList.add('active');
}

function closeModal() 
{
    document.getElementById('editModal').classList.remove('active');
}

function showAddModal() 
{
    document.getElementById('addModal').classList.add('active');
}

function quickAdd(key, placeholder, description) 
{
    document.getElementById('addModal').querySelector('[name="key"]').value = key;
    document.getElementById('addModal').querySelector('[name="value"]').placeholder = placeholder;
    document.getElementById('addModal').querySelector('[name="description"]').value = description;
    document.getElementById('addModal').classList.add('active');
}

function quickAddDynamicGroq() 
{
    const keyName = nextGroqKeyName();
    quickAdd(keyName, `Your Groq API Key (${keyName})`, 'Additional Groq key for rotation');
}

function nextGroqKeyName() 
{
    let index = 1;
    while (window.existingGroqKeys.includes(index === 1 ? 'GROQ_API_KEY' : `GROQ_API_KEY_${index}`)) 
    {
        index += 1;
    }
    return index === 1 ? 'GROQ_API_KEY' : `GROQ_API_KEY_${index}`;
}

function closeAddModal() 
{
    document.getElementById('addModal').classList.remove('active');
}


window.onclick = function(event) 
{
    if (event.target.classList.contains('modal')) 
    {
        event.target.classList.remove('active');
    }
}


async function verifyGroqKey(settingId) 
{
    try 
    {
        const res = await fetch('/settings/test-groq-key', 
        {
            method: 'POST',
            headers: 
            {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ id: settingId })
        });
        const data = await res.json();
        const msg = `${data.message} (status ${data.status})`;
        alert(msg);
    } 
    catch (e) 
    {
        alert('Verification failed: network or server error');
    }
}
