const fs = require('fs');
const path = require('path');

function walkSync(dir, callback) {
  fs.readdirSync(dir).forEach(file => {
    let filepath = path.join(dir, file);
    if (fs.statSync(filepath).isDirectory()) {
      walkSync(filepath, callback);
    } else {
      callback(filepath);
    }
  });
}

walkSync('resources/js', filepath => {
  if (filepath.endsWith('.vue')) {
    let content = fs.readFileSync(filepath, 'utf8');
    if (content.includes('accept="image/png, image/jpeg, image/jpg"')) {
      content = content.replace(/accept="image\/png, image\/jpeg, image\/jpg"/g, 'accept="image/png, image/jpeg, image/jpg, image/webp"');
      fs.writeFileSync(filepath, content, 'utf8');
      console.log('Updated: ' + filepath);
    }
  }
});
