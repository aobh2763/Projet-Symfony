import os
import re

PROJECT_DIR = '.'  # Change this to your project root if needed

# Regex for href or src attributes, capturing attribute name and URL
attr_pattern = re.compile(r'(href|src)=(["\'])(.*?)\2')

def replace_attr(match):
    attr = match.group(1)    # 'href' or 'src'
    quote = match.group(2)   # ' or "
    url = match.group(3)

    # Skip external or Twig dynamic URLs
    if url.startswith(('http://', 'https://', 'mailto:', '#', '{{', '{%')):
        return match.group(0)

    # If href and ends with .html, replace with .html.twig
    if attr == 'href' and url.endswith('.html'):
        url = url[:-5] + '.html.twig'

    # Wrap local URLs with asset()
    if not (url.startswith('{{') and url.endswith('}}')):
        url = "{{ asset('" + url + "') }}"

    return f'{attr}={quote}{url}{quote}'

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = attr_pattern.sub(replace_attr, content)

    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Processed: {filepath}")

def main():
    for root, dirs, files in os.walk(PROJECT_DIR):
        for file in files:
            if file.endswith('.twig'):
                process_file(os.path.join(root, file))

if __name__ == '__main__':
    main()
