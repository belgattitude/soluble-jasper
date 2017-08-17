#!/usr/bin/env bash

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="$SCRIPT_DIR/.."

# PHPJavabridge version
PJB_VERSION="7.1.3"
PJB_DIR="$SCRIPT_DIR/downloads/php-java-bridge-$PJB_VERSION"

JAVA_BIN=`which java`

JASPER_BRIDGE_WAR=${SCRIPT_DIR}/jasper_report_server.war

cd $SCRIPT_DIR;

buildJavaBridgeServer() {

    echo "[*] Download and build PHPJavaBridge";
    cd downloads;
    wget https://github.com/belgattitude/php-java-bridge/archive/$PJB_VERSION.zip -O pjb.zip;
    if [ -d "$PJB_DIR" ]; then
        rm -rf "$PJB_DIR";
    fi;
    unzip pjb.zip && cd php-java-bridge-$PJB_VERSION;
    ./gradlew clean
    ./gradlew war -I $SCRIPT_DIR/init.jasperreport.gradle

    mv ${PJB_DIR}/build/libs/JavaBridgeTemplate.war ${JASPER_BRIDGE_WAR}

    # restore path
    cd $SCRIPT_DIR;
}


buildJavaBridgeServer;
