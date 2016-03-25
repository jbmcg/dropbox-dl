#!/usr/bin/env sh
TARGET_FILE=$0

cd `dirname $TARGET_FILE`
TARGET_FILE=`basename $TARGET_FILE`

# Iterate down a (possible) chain of symlinks
while [ -L "$TARGET_FILE" ]
do
    TARGET_FILE=`readlink $TARGET_FILE`
    cd `dirname $TARGET_FILE`
    TARGET_FILE=`basename $TARGET_FILE`
done

# Compute the canonicalized name by finding the physical path for the directory we're in and appending the target file.
PHYS_DIR=`pwd -P`

# Install composer dependencies
cd ${PHYS_DIR}
`which composer` install

# Create symlink
ln -s ${PHYS_DIR}/bin/dropbox-dl /usr/local/bin/dropbox-dl

# Done
echo DONE.
