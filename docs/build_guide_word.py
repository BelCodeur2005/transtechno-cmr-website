"""
Convertit docs/guide-admin.md en guide-admin.docx
Usage : python docs/build_guide_word.py
"""

import re
import os
from docx import Document
from docx.shared import Pt, RGBColor, Inches, Cm, Emu
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_LINE_SPACING
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml.ns import qn
from docx.oxml import OxmlElement
from copy import deepcopy

# ── Couleurs Transtechno ─────────────────────────────────────────────────────
NAVY   = RGBColor(0x00, 0x4A, 0x80)
RED    = RGBColor(0xE3, 0x1E, 0x24)
WHITE  = RGBColor(0xFF, 0xFF, 0xFF)
GRAY_H = RGBColor(0xF0, 0xF4, 0xF8)   # fond en-tête de tableau
GRAY_R = RGBColor(0xFA, 0xFA, 0xFA)   # fond lignes alternées
DARK   = RGBColor(0x1A, 0x1A, 0x2E)
NOTE   = RGBColor(0xE8, 0xF4, 0xFF)   # fond blockquote

# ── Helpers XML ──────────────────────────────────────────────────────────────

def rgb_to_hex(rgb: RGBColor) -> str:
    # RGBColor est un tuple (r, g, b) en python-docx
    return '{:02X}{:02X}{:02X}'.format(rgb[0], rgb[1], rgb[2])

def set_cell_bg(cell, rgb: RGBColor):
    tc = cell._tc
    tcPr = tc.get_or_add_tcPr()
    shd = OxmlElement('w:shd')
    shd.set(qn('w:val'), 'clear')
    shd.set(qn('w:color'), 'auto')
    shd.set(qn('w:fill'), rgb_to_hex(rgb))
    tcPr.append(shd)

def set_cell_border(cell, **kwargs):
    tc = cell._tc
    tcPr = tc.get_or_add_tcPr()
    tcBorders = OxmlElement('w:tcBorders')
    for edge in ('top', 'left', 'bottom', 'right', 'insideH', 'insideV'):
        if edge in kwargs:
            el = OxmlElement(f'w:{edge}')
            el.set(qn('w:val'), kwargs[edge].get('val', 'single'))
            el.set(qn('w:sz'), str(kwargs[edge].get('sz', 4)))
            el.set(qn('w:space'), '0')
            el.set(qn('w:color'), kwargs[edge].get('color', '000000'))
            tcBorders.append(el)
    tcPr.append(tcBorders)

def add_run_inline(para, text: str, bold=False, italic=False,
                   color=None, font_size=None, mono=False):
    """Ajoute un run avec formatage inline."""
    run = para.add_run(text)
    run.bold = bold
    run.italic = italic
    if color:
        run.font.color.rgb = color
    if font_size:
        run.font.size = Pt(font_size)
    if mono:
        run.font.name = 'Courier New'
        run.font.size = Pt(9)
        run.font.color.rgb = RGBColor(0xC7, 0x25, 0x4E)
    return run

def parse_inline(para, text: str, base_color=None, base_size=10.5):
    """Parse **bold**, `code`, *italic* dans un paragraphe."""
    # Regex combiné : **bold** | `code` | *italic*
    pattern = re.compile(r'\*\*(.+?)\*\*|`([^`]+)`|\*(.+?)\*')
    last = 0
    for m in pattern.finditer(text):
        # texte avant le match
        if m.start() > last:
            r = para.add_run(text[last:m.start()])
            r.font.size = Pt(base_size)
            if base_color:
                r.font.color.rgb = base_color
        if m.group(1) is not None:          # **bold**
            r = para.add_run(m.group(1))
            r.bold = True
            r.font.size = Pt(base_size)
            if base_color:
                r.font.color.rgb = base_color
        elif m.group(2) is not None:        # `code`
            r = para.add_run(m.group(2))
            r.font.name = 'Courier New'
            r.font.size = Pt(9)
            r.font.color.rgb = RGBColor(0xC7, 0x25, 0x4E)
        elif m.group(3) is not None:        # *italic*
            r = para.add_run(m.group(3))
            r.italic = True
            r.font.size = Pt(base_size)
            if base_color:
                r.font.color.rgb = base_color
        last = m.end()
    # reste
    if last < len(text):
        r = para.add_run(text[last:])
        r.font.size = Pt(base_size)
        if base_color:
            r.font.color.rgb = base_color

def add_horizontal_line(doc):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after  = Pt(2)
    pPr = p._p.get_or_add_pPr()
    pBdr = OxmlElement('w:pBdr')
    bottom = OxmlElement('w:bottom')
    bottom.set(qn('w:val'), 'single')
    bottom.set(qn('w:sz'), '4')
    bottom.set(qn('w:space'), '1')
    bottom.set(qn('w:color'), '004A80')
    pBdr.append(bottom)
    pPr.append(pBdr)

# ── Styles de paragraphe ─────────────────────────────────────────────────────

def style_heading1(doc, text):
    """## Section"""
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(18)
    p.paragraph_format.space_after  = Pt(6)
    pPr = p._p.get_or_add_pPr()
    # Filet rouge en bas
    pBdr = OxmlElement('w:pBdr')
    bottom = OxmlElement('w:bottom')
    bottom.set(qn('w:val'), 'single')
    bottom.set(qn('w:sz'), '6')
    bottom.set(qn('w:space'), '1')
    bottom.set(qn('w:color'), 'E31E24')
    pBdr.append(bottom)
    pPr.append(pBdr)
    r = p.add_run(text)
    r.bold = True
    r.font.size = Pt(14)
    r.font.color.rgb = NAVY
    return p

def style_heading2(doc, text):
    """### Sous-section"""
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(12)
    p.paragraph_format.space_after  = Pt(4)
    r = p.add_run(text)
    r.bold = True
    r.font.size = Pt(11.5)
    r.font.color.rgb = NAVY
    return p

def style_heading3(doc, text):
    """#### Étape"""
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(8)
    p.paragraph_format.space_after  = Pt(3)
    r = p.add_run(text)
    r.bold = True
    r.font.size = Pt(10.5)
    r.font.color.rgb = RED
    return p

def style_blockquote(doc, text):
    """Blockquote > ..."""
    p = doc.add_paragraph()
    p.paragraph_format.left_indent  = Cm(0.8)
    p.paragraph_format.space_before = Pt(4)
    p.paragraph_format.space_after  = Pt(4)
    # Filet bleu à gauche
    pPr = p._p.get_or_add_pPr()
    pBdr = OxmlElement('w:pBdr')
    left = OxmlElement('w:left')
    left.set(qn('w:val'), 'single')
    left.set(qn('w:sz'), '12')
    left.set(qn('w:space'), '4')
    left.set(qn('w:color'), '004A80')
    pBdr.append(left)
    pPr.append(pBdr)
    clean = re.sub(r'^>\s*', '', text).strip()
    parse_inline(p, clean, base_color=RGBColor(0x33, 0x55, 0x77), base_size=10)
    return p

def add_md_table(doc, header_cells, data_rows):
    """Crée un tableau Word depuis les données markdown."""
    col_count = len(header_cells)
    table = doc.add_table(rows=1 + len(data_rows), cols=col_count)
    table.style = 'Table Grid'
    table.alignment = WD_TABLE_ALIGNMENT.LEFT

    # En-tête
    hdr = table.rows[0]
    for i, cell_text in enumerate(header_cells):
        cell = hdr.cells[i]
        set_cell_bg(cell, NAVY)
        cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
        p = cell.paragraphs[0]
        p.paragraph_format.space_before = Pt(3)
        p.paragraph_format.space_after  = Pt(3)
        clean = re.sub(r'\*\*(.+?)\*\*', r'\1', cell_text).strip()
        r = p.add_run(clean)
        r.bold = True
        r.font.size = Pt(9.5)
        r.font.color.rgb = WHITE

    # Données
    for ri, row_data in enumerate(data_rows):
        row = table.rows[ri + 1]
        bg = GRAY_R if ri % 2 == 1 else WHITE
        for ci, cell_text in enumerate(row_data):
            cell = row.cells[ci]
            set_cell_bg(cell, bg)
            cell.vertical_alignment = WD_ALIGN_VERTICAL.CENTER
            p = cell.paragraphs[0]
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after  = Pt(2)
            parse_inline(p, cell_text.strip(), base_size=9.5)

    doc.add_paragraph()   # espace après le tableau
    return table

# ── Page de garde ────────────────────────────────────────────────────────────

def add_cover_page(doc):
    # Bandeau navy
    p_band = doc.add_paragraph()
    p_band.paragraph_format.space_before = Pt(0)
    p_band.paragraph_format.space_after  = Pt(0)
    pPr = p_band._p.get_or_add_pPr()
    shd = OxmlElement('w:shd')
    shd.set(qn('w:val'), 'clear')
    shd.set(qn('w:color'), 'auto')
    shd.set(qn('w:fill'), '004A80')
    pPr.append(shd)
    p_band.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r = p_band.add_run('\n\nTRANSTECHNO CAMEROUN\n')
    r.font.size = Pt(22)
    r.font.bold = True
    r.font.color.rgb = WHITE
    r2 = p_band.add_run('Science Technology and Medical Instrument\n\n')
    r2.font.size = Pt(11)
    r2.font.color.rgb = RGBColor(0xCC, 0xDD, 0xEE)

    # Ligne rouge
    p_red = doc.add_paragraph()
    p_red.paragraph_format.space_before = Pt(0)
    p_red.paragraph_format.space_after  = Pt(0)
    pPr2 = p_red._p.get_or_add_pPr()
    shd2 = OxmlElement('w:shd')
    shd2.set(qn('w:val'), 'clear')
    shd2.set(qn('w:color'), 'auto')
    shd2.set(qn('w:fill'), 'E31E24')
    pPr2.append(shd2)
    p_red.add_run('  ')
    p_red.paragraph_format.space_before = Pt(0)
    p_red.paragraph_format.space_after  = Pt(0)

    doc.add_paragraph()

    # Titre principal
    p_title = doc.add_paragraph()
    p_title.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_title.paragraph_format.space_before = Pt(32)
    p_title.paragraph_format.space_after  = Pt(8)
    r_title = p_title.add_run('GUIDE ADMINISTRATEUR')
    r_title.font.size = Pt(28)
    r_title.font.bold = True
    r_title.font.color.rgb = NAVY

    p_sub = doc.add_paragraph()
    p_sub.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_sub.paragraph_format.space_before = Pt(0)
    p_sub.paragraph_format.space_after  = Pt(40)
    r_sub = p_sub.add_run('Manuel d\'utilisation du back-office')
    r_sub.font.size = Pt(14)
    r_sub.font.color.rgb = RGBColor(0x44, 0x66, 0x88)

    # Encadré "À qui s'adresse ce guide ?"
    p_box = doc.add_paragraph()
    p_box.paragraph_format.left_indent  = Cm(2)
    p_box.paragraph_format.right_indent = Cm(2)
    p_box.paragraph_format.alignment    = WD_ALIGN_PARAGRAPH.CENTER
    p_box.paragraph_format.space_before = Pt(20)
    p_box.paragraph_format.space_after  = Pt(4)
    pPr3 = p_box._p.get_or_add_pPr()
    shd3 = OxmlElement('w:shd')
    shd3.set(qn('w:val'), 'clear')
    shd3.set(qn('w:color'), 'auto')
    shd3.set(qn('w:fill'), 'E8F4FF')
    pPr3.append(shd3)
    r_box = p_box.add_run(
        'À qui s\'adresse ce guide ?\n'
        'À toute personne ayant un compte dans l\'interface admin du site transtechno.cm\n'
        '— commerciaux, techniciens SAV, éditeurs de contenu, responsable magasin.'
    )
    r_box.font.size = Pt(10.5)
    r_box.font.color.rgb = RGBColor(0x00, 0x40, 0x70)

    # Infos pied de garde
    p_info = doc.add_paragraph()
    p_info.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_info.paragraph_format.space_before = Pt(60)
    r_info = p_info.add_run('Version juin 2026   ·   transtechno.cm/admin   ·   office@transtechno-cmr.com')
    r_info.font.size = Pt(9)
    r_info.font.color.rgb = RGBColor(0x88, 0x99, 0xAA)

    doc.add_page_break()

# ── Sommaire statique ────────────────────────────────────────────────────────

def add_toc(doc, items):
    p_toc_title = doc.add_paragraph()
    p_toc_title.paragraph_format.space_before = Pt(0)
    p_toc_title.paragraph_format.space_after  = Pt(10)
    r = p_toc_title.add_run('TABLE DES MATIÈRES')
    r.font.size = Pt(14)
    r.font.bold = True
    r.font.color.rgb = NAVY

    add_horizontal_line(doc)
    doc.add_paragraph()

    for num, label in items:
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(3)
        p.paragraph_format.space_after  = Pt(3)
        p.paragraph_format.left_indent  = Cm(0.4)
        r_num = p.add_run(f'{num}.  ')
        r_num.font.bold = True
        r_num.font.size = Pt(10.5)
        r_num.font.color.rgb = RED
        r_lbl = p.add_run(label)
        r_lbl.font.size = Pt(10.5)
        r_lbl.font.color.rgb = DARK

    doc.add_page_break()

# ── Parsing principal ────────────────────────────────────────────────────────

def parse_md_table(lines, start):
    """Extrait un tableau markdown depuis la position start.
    Retourne (header_cells, data_rows, end_index)."""
    header_line = lines[start].strip()
    sep_idx     = start + 1
    if sep_idx >= len(lines) or not re.match(r'^\|[-| :]+\|$', lines[sep_idx].strip()):
        return None, None, start

    def split_row(line):
        return [c.strip() for c in line.strip().strip('|').split('|')]

    headers = split_row(header_line)
    rows    = []
    i = sep_idx + 1
    while i < len(lines):
        l = lines[i].strip()
        if l.startswith('|') and not re.match(r'^\|[-| :]+\|$', l):
            rows.append(split_row(l))
        elif l == '' or not l.startswith('|'):
            break
        i += 1
    return headers, rows, i

def build_docx(md_path, out_path):
    doc = Document()

    # Marges du document
    for section in doc.sections:
        section.top_margin    = Cm(2.0)
        section.bottom_margin = Cm(2.0)
        section.left_margin   = Cm(2.5)
        section.right_margin  = Cm(2.5)

    # Police par défaut
    style = doc.styles['Normal']
    style.font.name = 'Calibri'
    style.font.size = Pt(10.5)

    with open(md_path, encoding='utf-8') as f:
        lines = f.readlines()

    add_cover_page(doc)

    # Sommaire statique
    toc_items = [
        (1,  'Accès et connexion'),
        (2,  'Tableau de bord'),
        (3,  'Cotations — de la demande au devis envoyé'),
        (4,  'SAV & Maintenance — de l\'ouverture à la clôture'),
        (5,  'Catalogue & Produits'),
        (6,  'Actualités'),
        (7,  'Médiathèque'),
        (8,  'Messages Contact & Demandes Pôle'),
        (9,  'CRM Clients'),
        (10, 'Parc Installé'),
        (11, 'Utilisateurs & Rôles'),
        (12, 'Rôles et droits'),
        ('A', 'Annexe — Statuts et codes couleur'),
    ]
    add_toc(doc, toc_items)

    i = 0
    in_code_block  = False
    code_lines     = []
    bullet_level   = 0

    while i < len(lines):
        raw  = lines[i]
        line = raw.rstrip('\n')
        stripped = line.strip()

        # ── Bloc de code ````
        if stripped.startswith('```'):
            if not in_code_block:
                in_code_block = True
                code_lines = []
            else:
                # Fin du bloc
                in_code_block = False
                p = doc.add_paragraph()
                p.paragraph_format.space_before = Pt(4)
                p.paragraph_format.space_after  = Pt(4)
                p.paragraph_format.left_indent  = Cm(0.6)
                pPr = p._p.get_or_add_pPr()
                shd = OxmlElement('w:shd')
                shd.set(qn('w:val'), 'clear')
                shd.set(qn('w:color'), 'auto')
                shd.set(qn('w:fill'), 'F3F3F3')
                pPr.append(shd)
                r = p.add_run('\n'.join(code_lines))
                r.font.name = 'Courier New'
                r.font.size = Pt(8.5)
                r.font.color.rgb = RGBColor(0x33, 0x33, 0x33)
            i += 1
            continue

        if in_code_block:
            code_lines.append(line)
            i += 1
            continue

        # ── Titre principal #
        if stripped.startswith('# ') and not stripped.startswith('## '):
            # Déjà géré en page de garde — on ignore
            i += 1
            continue

        # ── H2 ##
        if stripped.startswith('## ') and not stripped.startswith('### '):
            text = stripped[3:].strip()
            # Supprimer les liens markdown [text](#anchor)
            text = re.sub(r'\[([^\]]+)\]\([^)]*\)', r'\1', text)
            style_heading1(doc, text)
            i += 1
            continue

        # ── H3 ###
        if stripped.startswith('### ') and not stripped.startswith('#### '):
            text = stripped[4:].strip()
            text = re.sub(r'\[([^\]]+)\]\([^)]*\)', r'\1', text)
            style_heading2(doc, text)
            i += 1
            continue

        # ── H4 ####
        if stripped.startswith('#### '):
            text = stripped[5:].strip()
            text = re.sub(r'\[([^\]]+)\]\([^)]*\)', r'\1', text)
            style_heading3(doc, text)
            i += 1
            continue

        # ── Séparateur ---
        if re.match(r'^-{3,}$', stripped):
            add_horizontal_line(doc)
            i += 1
            continue

        # ── Blockquote >
        if stripped.startswith('> ') or stripped == '>':
            style_blockquote(doc, line)
            i += 1
            continue

        # ── Tableau markdown |
        if stripped.startswith('|'):
            headers, rows, next_i = parse_md_table(lines, i)
            if headers:
                add_md_table(doc, headers, rows)
                i = next_i
                continue

        # ── Liste à puces -
        if re.match(r'^(\s*)[-*] ', line):
            indent_spaces = len(line) - len(line.lstrip())
            level = indent_spaces // 2
            content = re.sub(r'^\s*[-*] ', '', line).strip()
            p = doc.add_paragraph(style='List Bullet')
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after  = Pt(2)
            p.paragraph_format.left_indent  = Cm(0.4 + level * 0.4)
            # Puce colorée
            p.paragraph_format.first_line_indent = Cm(-0.4)
            # Contenu inline
            parse_inline(p, content, base_size=10.5)
            i += 1
            continue

        # ── Liste numérotée 1.
        if re.match(r'^\d+\. ', stripped):
            content = re.sub(r'^\d+\. ', '', stripped)
            num_match = re.match(r'^(\d+)\. ', stripped)
            num = int(num_match.group(1)) if num_match else 1
            p = doc.add_paragraph(style='List Number')
            p.paragraph_format.space_before = Pt(2)
            p.paragraph_format.space_after  = Pt(2)
            p.paragraph_format.left_indent  = Cm(0.4)
            parse_inline(p, content, base_size=10.5)
            i += 1
            continue

        # ── Ligne vide
        if stripped == '':
            i += 1
            continue

        # ── Paragraphe normal
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(3)
        p.paragraph_format.space_after  = Pt(5)
        parse_inline(p, stripped, base_size=10.5)
        i += 1

    # Pied de page sur toutes les sections
    for section in doc.sections:
        footer = section.footer
        fp = footer.paragraphs[0]
        fp.paragraph_format.alignment = WD_ALIGN_PARAGRAPH.CENTER
        fp.clear()
        r1 = fp.add_run('TRANSTECHNO CAMEROUN  ·  Guide Administrateur  ·  Version juin 2026')
        r1.font.size = Pt(8)
        r1.font.color.rgb = RGBColor(0x88, 0x99, 0xAA)

    doc.save(out_path)
    print(f'[OK] Document genere : {out_path}')

# ── Point d'entrée ───────────────────────────────────────────────────────────
if __name__ == '__main__':
    base = os.path.dirname(os.path.abspath(__file__))
    build_docx(
        md_path=os.path.join(base, 'guide-admin.md'),
        out_path=os.path.join(base, 'guide-admin.docx'),
    )
