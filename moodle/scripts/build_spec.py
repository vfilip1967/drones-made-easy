#!/usr/bin/env python3
"""Extract chapters from drones-made-easy.md and emit Moodle Book JSON specs."""
import json, re, sys, html

SRC = "/root/projects/drones-made-easy/drones-made-easy.md"

def md_to_html(md):
    lines = md.split("\n")
    out, i = [], 0
    in_code = False
    code_buf = []
    list_stack = []

    def close_lists():
        while list_stack:
            out.append(f"</{list_stack.pop()}>")

    while i < len(lines):
        line = lines[i]

        # fenced code
        if line.strip().startswith("```"):
            if in_code:
                out.append("<pre>" + html.escape("\n".join(code_buf)) + "</pre>")
                code_buf, in_code = [], False
            else:
                close_lists()
                in_code = True
            i += 1
            continue
        if in_code:
            code_buf.append(line)
            i += 1
            continue

        # table
        if line.strip().startswith("|") and i + 1 < len(lines) and re.match(r'^\s*\|[\s\-:|]+\|\s*$', lines[i+1]):
            close_lists()
            hdr = [c.strip() for c in line.strip().strip("|").split("|")]
            out.append('<table border="1" cellpadding="4" style="border-collapse:collapse"><thead><tr>')
            for c in hdr:
                out.append(f"<th>{inline(c)}</th>")
            out.append("</tr></thead><tbody>")
            i += 2
            while i < len(lines) and lines[i].strip().startswith("|"):
                cells = [c.strip() for c in lines[i].strip().strip("|").split("|")]
                out.append("<tr>" + "".join(f"<td>{inline(c)}</td>" for c in cells) + "</tr>")
                i += 1
            out.append("</tbody></table>")
            continue

        s = line.strip()

        if not s:
            close_lists()
            i += 1
            continue

        m = re.match(r'^(#{1,6})\s+(.*)$', s)
        if m:
            close_lists()
            lvl = min(len(m.group(1)) + 1, 6)
            out.append(f"<h{lvl}>{inline(m.group(2))}</h{lvl}>")
            i += 1
            continue

        if s.startswith(">"):
            close_lists()
            quote = []
            while i < len(lines) and lines[i].strip().startswith(">"):
                quote.append(lines[i].strip().lstrip(">").strip())
                i += 1
            body = "\n".join(quote)
            body = re.sub(r'^```|```$', '', body).strip()
            out.append('<blockquote style="border-left:3px solid #888;padding-left:1em">'
                       + "<pre>" + html.escape(body) + "</pre></blockquote>")
            continue

        m = re.match(r'^(\d+)\.\s+(.*)$', s)
        if m:
            if not list_stack or list_stack[-1] != "ol":
                close_lists(); out.append("<ol>"); list_stack.append("ol")
            out.append(f"<li>{inline(m.group(2))}</li>")
            i += 1
            continue

        m = re.match(r'^[-*]\s+(.*)$', s)
        if m:
            if not list_stack or list_stack[-1] != "ul":
                close_lists(); out.append("<ul>"); list_stack.append("ul")
            out.append(f"<li>{inline(m.group(1))}</li>")
            i += 1
            continue

        if re.match(r'^-{3,}$', s):
            close_lists(); out.append("<hr>"); i += 1; continue

        close_lists()
        out.append(f"<p>{inline(s)}</p>")
        i += 1

    close_lists()
    if in_code and code_buf:
        out.append("<pre>" + html.escape("\n".join(code_buf)) + "</pre>")
    return "\n".join(out)

def inline(t):
    t = html.escape(t)
    t = re.sub(r'`([^`]+)`', r'<code>\1</code>', t)
    t = re.sub(r'\*\*([^*]+)\*\*', r'<strong>\1</strong>', t)
    t = re.sub(r'(?<!\*)\*([^*]+)\*(?!\*)', r'<em>\1</em>', t)
    t = re.sub(r'\[([^\]]+)\]\(([^)]+)\)', r'<a href="\2">\1</a>', t)
    return t

def main():
    text = open(SRC, encoding="utf-8").read()
    lines = text.split("\n")
    heads = []
    for idx, l in enumerate(lines):
        m = re.match(r'^## (Κεφάλαιο|Παράρτημα) ([^\s:]+)[:\s]', l)
        if m:
            heads.append((idx, m.group(2), l[3:].strip()))
    chapters = {}
    for n, (idx, key, title) in enumerate(heads):
        end = heads[n+1][0] if n + 1 < len(heads) else len(lines)
        chapters[key] = {"title": title, "body": "\n".join(lines[idx+1:end]).strip()}

    wanted = json.loads(sys.argv[1])   # {"bookname":..., "intro":..., "chapters":["1","4",...]}
    spec = {"name": wanted["name"], "intro": wanted["intro"], "chapters": []}
    for key in wanted["chapters"]:
        if key not in chapters:
            sys.exit(f"chapter {key} not found; have: {sorted(chapters)}")
        c = chapters[key]
        spec["chapters"].append({"title": c["title"], "content": md_to_html(c["body"]), "subchapter": 0})
    json.dump(spec, open(sys.argv[2], "w", encoding="utf-8"), ensure_ascii=False)
    print(f"wrote {sys.argv[2]}: {len(spec['chapters'])} chapters")

main()
