// Build emifree-landing.zip using 7-Zip with a real staging tree.
// Excluded: node_modules, .git, .impeccable, tests/, planning *.md docs,
// scratch _test-*.html, build script itself, dev-only assets/blog_de/*.md.
import { promises as fs } from 'fs';
import path from 'path';
import { spawnSync } from 'child_process';

const SRC = process.argv[2] || '.';
const OUT_DIR = process.argv[3] || '.';
const TOPDIR = 'emifree-landing';
const STAGING = path.join(OUT_DIR, '_zip_staging_' + TOPDIR);
const OUT = path.join(OUT_DIR, TOPDIR + '.zip');

const EXCLUDE_DIRS = new Set([
  'node_modules', '.git', '.impeccable', '.vscode', '.idea',
  'tests', // dev-only smoke scripts
]);
const EXCLUDE_FILES = new Set(['package-lock.json']);
const EXCLUDE_PREFIXES = ['_test-', '_build-'];
const EXCLUDE_PATHS = [
  /^assets[/\\]blog_de[/\\].*\.md$/i,
  /^Handover.*\.md$/i,
  /^NEXT_AGENT_HANDOFF.*\.md$/i,
  /^NEXT_AGENT_HANDOVER.*\.md$/i,
  /^RELEASE_NOTE\.md$/i,
  /^Blog\d+_de\.md$/i,
  // Built theme zips — if these exist in the working tree (e.g. the
  // user dropped the previous build alongside the source), exclude
  // them so the next build doesn't compound in size. Same for
  // extracted zip directories.
  /^emifree-landing[^/\\]*\.zip$/i,
  /^emifree-landing[^/\\]*$/i,
];

async function walk(dir, base = dir) {
  const ents = await fs.readdir(dir, { withFileTypes: true });
  const out = [];
  for (const ent of ents) {
    const abs = path.join(dir, ent.name);
    const rel = path.relative(base, abs);
    if (ent.isDirectory()) {
      if (EXCLUDE_DIRS.has(ent.name)) continue;
      out.push(...(await walk(abs, base)));
    } else if (ent.isFile()) {
      if (EXCLUDE_FILES.has(ent.name)) continue;
      if (EXCLUDE_PREFIXES.some(p => ent.name.startsWith(p))) continue;
      const relNorm = rel.replaceAll('\\', '/');
      if (EXCLUDE_PATHS.some(rx => rx.test(relNorm))) continue;
      out.push(rel);
    }
  }
  return out;
}

const relFiles = await walk(SRC);
console.log('Including ' + relFiles.length + ' files…');

// Build a real staging tree TOPDIR/file... so the zip naturally has the
// wrapper. Use copyFileSync to handle cross-device copy safely.
await fs.rm(STAGING, { recursive: true, force: true });
await fs.mkdir(path.join(STAGING, TOPDIR), { recursive: true });

for (const rel of relFiles) {
  const src = path.join(SRC, rel);
  const dst = path.join(STAGING, TOPDIR, rel);
  await fs.mkdir(path.dirname(dst), { recursive: true });
  await fs.copyFile(src, dst);
}

// 7-Zip: archive the staging top dir directly. Use forward slashes for
// the entry so the zip uses POSIX separators (cross-platform friendliness).
const rc = spawnSync(
  'C:/Program Files/7-Zip/7z.exe',
  ['a', '-tzip', '-mx=5', '-r', OUT, `${TOPDIR}/*`],
  { cwd: STAGING, stdio: 'inherit' },
);

// 7z with cwd=STAGING + relative OUT writes the zip INSIDE the staging
// dir, but the rest of the script expects it at the OUT path. Move it
// before cleaning up staging so the zip survives.
const stagingZip = path.join(STAGING, TOPDIR + '.zip');
try {
  await fs.rename(stagingZip, OUT);
} catch (e) {
  if (e.code !== 'ENOENT') throw e;
}

// Clean up staging regardless of outcome
await fs.rm(STAGING, { recursive: true, force: true });

if (rc.status !== 0) {
  console.error('Zip creation failed (exit ' + rc.status + ')');
  process.exit(1);
}
const stat = await fs.stat(OUT);
console.log(`Built: ${OUT}`);
console.log(`Size:  ${(stat.size / 1024 / 1024).toFixed(2)} MB`);
console.log(`Top:   ${TOPDIR}/`);
