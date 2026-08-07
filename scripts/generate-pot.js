const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const pluginRoot = path.join(root, 'src', 'includes', 'Plugins');
const catalogs = [
  { output: path.join(root, 'src', 'languages', 'wikipress.pot'), source: path.join(root, 'src'), name: 'WikiPress', domain: 'wikipress' },
  ...fs.readdirSync(pluginRoot, { withFileTypes: true })
    .filter((entry) => entry.isDirectory())
    .map((entry) => {
      const languageDirectory = path.join(pluginRoot, entry.name, 'Language');
      const existingCatalog = fs.readdirSync(languageDirectory, { withFileTypes: true })
        .find((file) => file.isFile() && file.name.toLowerCase().endsWith('.pot'));
      const domains = {
        Demo: 'wiki-demo-plugin',
        Elementor: 'wikipress-elementor',
        FontAwesome: 'wikipress-fontawesome',
        Gutenburg: 'wikipress',
        InternalWiki: 'internal-wiki-plugin',
        UserRolesManager: 'wikipress',
      };
      return {
        output: path.join(languageDirectory, entry.name === 'FontAwesome' ? 'Fontawesome.pot' : (existingCatalog ? existingCatalog.name : `${entry.name}.pot`)),
        source: path.join(pluginRoot, entry.name),
        name: entry.name,
        domain: domains[entry.name] || 'wikipress',
      };
    })
    .filter((catalog) => fs.existsSync(path.dirname(catalog.output))),
];

const ignoredDirectories = new Set(['vendor', 'node_modules', 'dist', 'build']);
const sourceExtensions = new Set(['.php', '.js', '.json']);

function filesIn(directory) {
  return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    const fullPath = path.join(directory, entry.name);
    if (entry.isDirectory()) {
      return ignoredDirectories.has(entry.name) ? [] : filesIn(fullPath);
    }
    return sourceExtensions.has(path.extname(entry.name)) ? [fullPath] : [];
  });
}

function decode(value) {
  return value.replace(/\\(['"\\])/g, '$1').replace(/\\n/g, '\\n');
}

function addEntry(entries, sourceFile, line, singular, plural = null, context = null) {
  const key = JSON.stringify([singular, plural, context]);
  if (!entries.has(key)) entries.set(key, { singular, plural, context, references: [] });
  entries.get(key).references.push(`${sourceFile}:${line}`);
}

function extract(file, sourceRoot, entries) {
  const content = fs.readFileSync(file, 'utf8');
  const relative = path.relative(root, file).replaceAll('\\', '/');
  const lines = content.split(/\r?\n/);
  const add = (line, singular, plural = null, context = null) => {
    if (singular && !/^https?:\/\//.test(singular)) addEntry(entries, relative, line, decode(singular), plural ? decode(plural) : null, context ? decode(context) : null);
  };
  const simple = /(?:__|_e|esc_html__|esc_attr__)\s*\(\s*(['"])((?:\\.|(?!\1)[\s\S])*?)\1\s*,/g;
  const contextual = /_x\s*\(\s*(['"])((?:\\.|(?!\1)[\s\S])*?)\1\s*,\s*(['"])((?:\\.|(?!\3)[\s\S])*?)\3\s*,/g;
  const plural = /_n\s*\(\s*(['"])((?:\\.|(?!\1)[\s\S])*?)\1\s*,\s*(['"])((?:\\.|(?!\3)[\s\S])*?)\3\s*,/g;
  lines.forEach((lineText, index) => {
    let match;
    while ((match = contextual.exec(lineText))) add(index + 1, match[2], null, match[4]);
    while ((match = plural.exec(lineText))) add(index + 1, match[2], match[4]);
    while ((match = simple.exec(lineText))) add(index + 1, match[2]);
    contextual.lastIndex = 0;
    plural.lastIndex = 0;
    simple.lastIndex = 0;
  });
}

function quote(value) {
  return value.replace(/\\/g, '\\\\').replace(/"/g, '\\"').replace(/\n/g, '\\n');
}

function writeCatalog(catalog) {
  const entries = new Map();
  const sourceFiles = filesIn(catalog.source).filter((file) => {
    if (catalog.name === 'WikiPress' && file.includes(`${path.sep}src${path.sep}includes${path.sep}Plugins${path.sep}`)) return false;
    return true;
  });
  sourceFiles.forEach((file) => extract(file, catalog.source, entries));
  const sorted = [...entries.values()].sort((a, b) => a.singular.localeCompare(b.singular));
  const header = [
    `# Translation catalog for ${catalog.name}.`,
    '# Copyright (C) 2026 WikiPress contributors',
    '# This file is distributed under the same license as the WikiPress package.',
    'msgid ""',
    'msgstr ""',
    '"Project-Id-Version: WikiPress 1.0.0\\n"',
    '"Content-Type: text/plain; charset=UTF-8\\n"',
    '"Content-Transfer-Encoding: 8bit\\n"',
    `"X-Domain: ${catalog.domain}\\n"`,
    '',
  ];
  const body = sorted.map((entry) => {
    const refs = [...new Set(entry.references)].sort().map((ref) => `#: ${ref}`).join('\n');
    const lines = [refs];
    if (entry.context) lines.push(`msgctxt "${quote(entry.context)}"`);
    lines.push(`msgid "${quote(entry.singular)}"`);
    if (entry.plural) {
      lines.push(`msgid_plural "${quote(entry.plural)}"`);
      lines.push('msgstr[0] ""', 'msgstr[1] ""');
    } else {
      lines.push('msgstr ""');
    }
    return lines.join('\n');
  }).join('\n\n');
  fs.writeFileSync(catalog.output, `${header.join('\n')}${body}\n`, 'utf8');
  console.log(`${path.relative(root, catalog.output)}: ${sorted.length} entries`);
}

catalogs.forEach(writeCatalog);
