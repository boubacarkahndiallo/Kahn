const fs = require('fs');
const s = fs.readFileSync('./public/js/client.js', 'utf8');
let i = 0, n = s.length; let tries = []; let inSingle = false, inDouble = false, inBack = false, inLineComment = false, inBlockComment = false;
function isWordAt(pos, word) { return s.substr(pos, word.length) === word && /[\s\(\{]/.test(s[pos + word.length] || ' '); }
while (i < n) {
    const ch = s[i]; if (inLineComment) { if (ch === '\n') inLineComment = false; i++; continue; } if (inBlockComment) { if (ch === '*' && s[i + 1] === '/') { inBlockComment = false; i += 2; continue; } i++; continue; } if (inSingle) { if (ch === '\\' && s[i + 1]) { i += 2; continue; } if (ch === "'") inSingle = false; i++; continue; } if (inDouble) { if (ch === '\\' && s[i + 1]) { i += 2; continue; } if (ch === '"') inDouble = false; i++; continue; } if (inBack) { if (ch === '\\' && s[i + 1]) { i += 2; continue; } if (ch === '`') inBack = false; i++; continue; } if (ch === '/') { if (s[i + 1] === '/') { inLineComment = true; i += 2; continue; } if (s[i + 1] === '*') { inBlockComment = true; i += 2; continue; } }
    if (ch === "'") { inSingle = true; i++; continue; } if (ch === '"') { inDouble = true; i++; continue; } if (ch === '`') { inBack = true; i++; continue; }
    if (isWordAt(i, 'try')) { tries.push({ pos: i, line: s.slice(0, i).split('\n').length, found: false }); i += 3; continue; }
    if (isWordAt(i, 'catch')) { for (let j = tries.length - 1; j >= 0; j--) { if (!tries[j].found) { tries[j].found = true; break; } } i += 5; continue; }
    if (isWordAt(i, 'finally')) { for (let j = tries.length - 1; j >= 0; j--) { if (!tries[j].found) { tries[j].found = true; break; } } i += 7; continue; }
    i++;
}
const unmatched = tries.filter(t => !t.found);
console.log('Unmatched tries count', unmatched.length);
if (unmatched.length > 0) { console.log('lines: ' + unmatched.map(u => u.line).join(', ')); }
else { console.log('All try blocks have catch/finally'); }
