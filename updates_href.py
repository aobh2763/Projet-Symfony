import os
import re

# Directory of your project (change if needed)
PROJECT_DIR = '.'

# Regex to find href attributes
# We'll capture href="..." or href='...' with a group for the URL inside
href_pattern = re.compile(r'href=(["\'])(.*?)\1')

def replace_href(match):
    quote = match.group(1)
    url = match.group(2)

    # Skip external links (starting with http://, https://, mailto:, #, or Twig variables {{ ... }})
    if url.startswith(('http://', 'https://', 'mailto:', '#', '{{', '{%')):
        return match.group(0)

    # Change .html to .html.twig if applicable
    if url.endswith('.html'):
        url = url[:-5] + '.html.twig'

    # Wrap with asset() if not already
    # If href is already Twig expression, skip
    if not (url.startswith('{{') and url.endswith('}}')):
        url = "{{ asset('" + url + "') }}"

    return f'href={quote}{url}{quote}'

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    new_content = href_pattern.sub(replace_href, content)

    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Processed: {filepath}")

def main():
    for root, dirs, files in os.walk(PROJECT_DIR):
        for file in files:
            if file.endswith('.twig'):
                full_path = os.path.join(root, file)
                process_file(full_path)

if __name__ == "__main__":
    main()
