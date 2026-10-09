#!/usr/bin/env bash
# Build a com_kjeholtbusiness install package.
#
# Steps:
#   1. Bump the patch version in kjeholtbusiness.xml (0.2.9.3 -> 0.2.9.4 ...)
#      (or pass a full version as the first argument to set it explicitly)
#   2. Set <creationDate> to today
#   3. Create sql/updates/mysql/<version>.sql schema marker
#   4. Create the install zip in dist/
#
# Usage:
#   ./build.sh              # auto-bump patch level, zip to dist/
#   ./build.sh 0.3.0        # use an explicit version
set -euo pipefail

MANIFEST="kjeholtbusiness.xml"
UPDATE_DIR="administrator/components/com_kjeholtbusiness/sql/updates/mysql"
DIST_DIR="dist"
TODAY=$(date +%Y-%m-%d)

if [ ! -f "$MANIFEST" ]; then
    echo "ERROR: $MANIFEST not found - run this from the repository root." >&2
    exit 1
fi

CURRENT=$(sed -n 's/.*<version>\(.*\)<\/version>.*/\1/p' "$MANIFEST" | head -1)

if [ $# -ge 1 ]; then
    NEW_VERSION="$1"
else
    PREFIX=$(echo "$CURRENT" | awk -F. 'OFS="."{$NF=""; print substr($0, 1, length($0)-1)}')
    PATCH=$(echo "$CURRENT" | awk -F. '{print $NF}')
    NEW_VERSION="$PREFIX.$((PATCH + 1))"
fi

echo "Building com_kjeholtbusiness $CURRENT -> $NEW_VERSION ($TODAY)"

sed -i "s|<creationDate>[^<]*</creationDate>|<creationDate>$TODAY</creationDate>|" "$MANIFEST"
sed -i "s|<version>$CURRENT</version>|<version>$NEW_VERSION</version>|" "$MANIFEST"

if ! grep -q "<version>$NEW_VERSION</version>" "$MANIFEST"; then
    echo "ERROR: version replacement failed" >&2
    exit 1
fi

# Schema marker so the updater registers the new version
sed "s/0\.2\.9\.1/$NEW_VERSION/" "$UPDATE_DIR/0.2.9.1.sql" > "$UPDATE_DIR/$NEW_VERSION.sql"

mkdir -p "$DIST_DIR"

PKG="com_kjeholtbusiness-$NEW_VERSION.zip"
rm -f "$DIST_DIR/$PKG"

# Package the files declared in the manifest (component + plugin + script + media)
zip -r -q "$DIST_DIR/$PKG" \
    "$MANIFEST" \
    script.php \
    administrator/components/com_kjeholtbusiness \
    components/com_kjeholtbusiness \
    plugins/content/kjeholtbusiness \
    media/com_kjeholtbusiness \
    -x "*/.*" "*.DS_Store"

echo
echo "OK: $DIST_DIR/$PKG"
unzip -l "$DIST_DIR/$PKG" | tail -3

cat <<EOF

Next steps:
  1. Review and commit the version bump:
       git add -A && git commit -m "Release $NEW_VERSION" && git push
  2. Install $DIST_DIR/$PKG in Joomla and verify the version under
     System -> Install -> Extensions (or the com_kjeholtbusiness log lines).
EOF
