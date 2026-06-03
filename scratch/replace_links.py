import os
import re

files_to_update = [
    'index.php',
    'about.php',
    'product.php',
    'gallery.php',
    'contact.php',
    'form.php'
]

replacements = {
    r'\bindex\.html\b': 'index.php',
    r'\babout\.html\b': 'about.php',
    r'\bproduct\.html\b': 'product.php',
    r'\bgallery\.html\b': 'gallery.php',
    r'\bcontact\.html\b': 'contact.php',
    r'\bform\.html\b': 'form.php'
}

cwd = r"d:\KULIAH\TUGAS\SEMESTER 4\PEMROGRAMAN WEB\EAS PEMWEB"

for filename in files_to_update:
    filepath = os.path.join(cwd, filename)
    if not os.path.exists(filepath):
        print(f"File not found: {filepath}")
        continue
        
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    original = content
    for pattern, replacement in replacements.items():
        content = re.sub(pattern, replacement, content)
        
    if original != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated links in: {filename}")
    else:
        print(f"No replacements made in: {filename}")
