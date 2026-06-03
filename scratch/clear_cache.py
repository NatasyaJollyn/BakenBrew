import re
import os

cwd = r"d:\KULIAH\TUGAS\SEMESTER 4\PEMROGRAMAN WEB\EAS PEMWEB"

files_to_update = [
    'index.php',
    'about.php',
    'product.php',
    'gallery.php',
    'contact.php',
    'form.php'
]

for filename in files_to_update:
    filepath = os.path.join(cwd, filename)
    if not os.path.exists(filepath):
        print(f"File not found: {filepath}")
        continue
        
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    # Increment style.css version
    content = content.replace("css/style.css?v=3.0", "css/style.css?v=4.0")
    
    # Add version to script.js
    content = content.replace("js/script.js", "js/script.js?v=4.0")
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Successfully bumped cache buster in: {filename}")
