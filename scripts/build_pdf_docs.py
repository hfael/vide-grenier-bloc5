from pathlib import Path
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import getSampleStyleSheet
from reportlab.platypus import Image, SimpleDocTemplate, Paragraph, Spacer


DOCS = [
    "tests.md",
    "recette.md",
    "mise_en_production.md",
    "guide_utilisateur.md",
    "release_notes.md",
]


def markdown_to_paragraphs(text):
    lines = text.splitlines()
    in_code = False

    for line in lines:
        raw = line.rstrip()
        if raw.startswith("```"):
            in_code = not in_code
            continue
        if not raw:
            yield ("spacer", "")
            continue
        if in_code:
            yield ("code", raw.replace("&", "&amp;").replace("<", "&lt;").replace(">", "&gt;"))
            continue
        if raw.startswith("# "):
            yield ("title", raw[2:])
        elif raw.startswith("## "):
            yield ("heading", raw[3:])
        elif raw.startswith("![") and "](" in raw and raw.endswith(")"):
            image_path = raw.split("](", 1)[1][:-1]
            yield ("image", image_path)
        elif raw.startswith("- "):
            yield ("body", "* " + raw[2:])
        elif raw[0:2].isdigit() and ". " in raw[:4]:
            yield ("body", raw)
        else:
            yield ("body", raw)


def build_pdf(source, target):
    styles = getSampleStyleSheet()
    story = []

    for kind, value in markdown_to_paragraphs(source.read_text(encoding="utf-8")):
        if kind == "spacer":
            story.append(Spacer(1, 8))
        elif kind == "title":
            story.append(Paragraph(value, styles["Title"]))
            story.append(Spacer(1, 12))
        elif kind == "heading":
            story.append(Paragraph(value, styles["Heading2"]))
            story.append(Spacer(1, 8))
        elif kind == "code":
            story.append(Paragraph("<font name='Courier'>" + value + "</font>", styles["Code"]))
        elif kind == "image":
            image_file = source.parent / value
            if image_file.exists():
                story.append(Image(str(image_file), width=460, height=300, kind="proportional"))
                story.append(Spacer(1, 10))
        else:
            story.append(Paragraph(value, styles["BodyText"]))
            story.append(Spacer(1, 4))

    target.parent.mkdir(parents=True, exist_ok=True)
    SimpleDocTemplate(str(target), pagesize=A4).build(story)


def main():
    docs_dir = Path("docs")
    pdf_dir = docs_dir / "pdf"

    for doc in DOCS:
        build_pdf(docs_dir / doc, pdf_dir / doc.replace(".md", ".pdf"))


if __name__ == "__main__":
    main()
