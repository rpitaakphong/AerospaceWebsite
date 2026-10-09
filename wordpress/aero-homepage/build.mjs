import {readFile,writeFile,mkdir,copyFile} from 'node:fs/promises';
import {fileURLToPath} from 'node:url';
import path from 'node:path';
import {execFileSync} from 'node:child_process';
// Scope this preview's ordinary rules and nested media queries without dependencies.
function scopeCSS(input) {
  const css = input.replace(/\/\*[\s\S]*?\*\//g,'');
  let result = '', cursor = 0;
  while (cursor < css.length) {
    const open = css.indexOf('{',cursor);
    if (open < 0) { result += css.slice(cursor); break; }
    const selector = css.slice(cursor,open).trim();
    let depth = 1, close = open + 1, quote = '';
    for (; close < css.length && depth; close++) {
      const char = css[close];
      if (quote) { if (char === quote && css[close-1] !== '\\') quote = ''; continue; }
      if (char === '"' || char === "'") { quote = char; continue; }
      if (char === '{') depth++;
      if (char === '}') depth--;
    }
    if (depth) throw new Error('Unbalanced CSS');
    const body = css.slice(open+1,close-1);
    if (selector.startsWith('@')) {
      if (!/^@(media|supports|layer)\b/.test(selector)) throw new Error(`Unsupported rule ${selector}`);
      result += selector+'{'+scopeCSS(body)+'}\n';
    } else {
      const scoped = selector.split(',').map(s => {
        s = s.trim();
        return [':root','body','html'].includes(s) ? '.aero-home' : '.aero-home '+s;
      }).join(',');
      result += scoped+'{'+body+'}\n';
    }
    cursor = close;
  }
  return result;
}
const root = path.dirname(fileURLToPath(import.meta.url));
const preview = path.resolve(root, '../../homepage-preview');
let html = await readFile(path.join(preview,'index.html'),'utf8');
html = html.match(/<main id="main">[\s\S]*?<\/main>/)[0]
  .replace('<main id="main">','<main id="content" class="aero-home" tabindex="-1">');
html = html.replace(/<div class="news__grid"[\s\S]*?(?=\n        <\/div>\n      <\/section>)/,
  '<div class="news__grid" id="news-grid"><?php aero_home_news(); ?></div>');
const assets = [...new Set([...html.matchAll(/src="assets\/([^"]+)"/g)].map(m => m[1]))];
await mkdir(path.join(root,'assets'),{recursive:true});
for (const file of assets) {
  const webFile = file.endsWith('.jpg') && file !== 'abet-eac-logo.jpg' ? file.replace(/\.jpg$/,'.webp') : file;
  if (webFile !== file) {
    execFileSync('cwebp',['-quiet','-q','90',path.join(preview,'assets',file),'-o',path.join(root,'assets',webFile)]);
  } else {
    await copyFile(path.join(preview,'assets',file),path.join(root,'assets',file));
  }
  html = html.replaceAll(`src="assets/${file}"`, `src="assets/${webFile}"`);
}
html = html.replace(/src="assets\/([^"]+)"/g, (_, file) =>
  `src="<?php echo esc_url(aero_home_asset('${file}')); ?>"`);
await writeFile(path.join(root,'templates/content.php'),
  '<?php if (!defined(\'ABSPATH\')) { exit; } ?>\n'+html+'\n');

let css = '';
for (const file of ['styles.css','hero-mosaic.css','accreditation.css']) {
  css += scopeCSS(await readFile(path.join(preview,file),'utf8'))+'\n';
}
css += await readFile(path.join(root,'integration.css'),'utf8');
await writeFile(path.join(root,'assets/homepage.css'),css);
console.log(`Built full-code homepage with ${assets.length} assets and scoped CSS.`);
