const fs = require('fs'); const s = fs.readFileSync('./public/js/client.js', 'utf8'); const count = (s.match(/`/g) || []).length; console.log('Backticks:', count);
