#!/bin/bash

# Cleanup.
START_DIR=$(pwd)
if [ ! -z $TRAVIS_BUILD_DIR ] ; then
    START_DIR=$TRAVIS_BUILD_DIR
fi

# Set up the basic structure. 
BUILD_DIR=$(mktemp -d /tmp/aop.XXXXXXXX)
echo "Using build directory ${BUILD_DIR}"
for DIR in htdocs bin
do
    mkdir -p "${BUILD_DIR}/$DIR"
done
PATH="$PATH:$BUILD_DIR/bin"

# Grab our binary dependencies.
wget -P "${BUILD_DIR}/bin" "https://phar.phpunit.de/phpunit.phar"
wget -P "${BUILD_DIR}/bin" "http://files.magerun.net/n98-magerun-latest.phar" 
wget -P "${BUILD_DIR}/bin" "https://raw.githubusercontent.com/colinmollenhour/modman/master/modman"
wget -P "${BUILD_DIR}/bin" "https://getcomposer.org/composer.phar"
chmod +x -R "$BUILD_DIR/bin"

# Setup magento.
INSTALL_DIR="${BUILD_DIR}/htdocs"
n98-magerun-latest.phar install \
      --dbHost="${MAGENTO_DB_HOST}" \
      --dbUser="${MAGENTO_DB_USER}" \
      --dbPass="${MAGENTO_DB_PASS}" \
      --dbName="${MAGENTO_DB_NAME}" \
      --installSampleData=no \
      --useDefaultConfigParams=yes \
      --magentoVersionByName="${MAGENTO_VERSION}" \
      --installationFolder="${INSTALL_DIR}" \
      --baseUrl="http://aop.local/" || { echo "Installing Magento failed"; exit 1; }

# Get our stuff in place.
cp composer.json "${INSTALL_DIR}" 
cp phpunit.xml "${INSTALL_DIR}"
cp local.xml.phpunit "${INSTALL_DIR}/app/etc/"

# Deploy module and dependencies.
cd "${INSTALL_DIR}"
modman init
modman link "${START_DIR}"
composer install --no-interaction --prefer-dist -v || { echo "Running composer failed."; exit 1; }

# Only run our own tests.
sed -i -e s/true/false/g app/etc/modules/EcomDev_PHPUnitTest.xml
n98-magerun-latest.phar cache:flush

# Now actually run the tests.
ECOMDEV_PHPUNIT_CUSTOM_BOOTSTRAP=app/code/community/Danslo/Aop/bootstrap.php phpunit
