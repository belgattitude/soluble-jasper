#!/usr/bin/env bash

set -e

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_DIR="${SCRIPT_DIR}/.."


# Webapp runner properties
WEBAPP_RUNNER_VERSION="8.5.15.1";
WEBAPP_RUNNER_URL="http://central.maven.org/maven2/com/github/jsimone/webapp-runner/${WEBAPP_RUNNER_VERSION}/webapp-runner-${WEBAPP_RUNNER_VERSION}.jar"
WEBAPP_RUNNER_JAR="${SCRIPT_DIR}/downloads/webapp-runner.jar"
WEBAPP_RUNNER_PORT=8083
WEBAPP_RUNNER_LOGFILE="${SCRIPT_DIR}/webapp-runner.${WEBAPP_RUNNER_PORT}.log"
WEBAPP_RUNNER_PIDFILE="${SCRIPT_DIR}/webapp-runner.${WEBAPP_RUNNER_PORT}.pid"

JAVA_BIN=`which java`

JASPER_BRIDGE_WAR="${SCRIPT_DIR}/jasper_report_server.war"


cd $SCRIPT_DIR;

downloadWebappRunner() {

    echo "[*] Download WebappRunner";
    if [ ! -f $WEBAPP_RUNNER_JAR ]; then
        wget $WEBAPP_RUNNER_URL -O $WEBAPP_RUNNER_JAR
    fi;
}

runJavaBridgeServerInBackground() {

    echo "[*] Starting JavaBridge server with webapp-runner (in background)";

    if [ ! -f $JASPER_BRIDGE_WAR ]; then
        echo "[*] Error: ${JASPER_BRIDGE_WAR} not present";
        exit 10;
    fi;

    if [ ! -f $WEBAPP_RUNNER_JAR ]; then
        echo "[*] Error: ${WEBAPP_RUNNER_JAR} not present";
        exit 11;
    fi;

    CMD="${JAVA_BIN} -jar ${WEBAPP_RUNNER_JAR} ${JASPER_BRIDGE_WAR} --port ${WEBAPP_RUNNER_PORT}";

    # Starting in background
    eval "${CMD} >${WEBAPP_RUNNER_LOGFILE} 2>&1 &disown; echo \$! > $WEBAPP_RUNNER_PIDFILE"

    SERVER_PID=`cat $WEBAPP_RUNNER_PIDFILE`;

    echo "[*] Server starter with PID ${SERVER_PID}";
    echo "    and command: ${CMD}";
}


# Here's the steps
downloadWebappRunner;
runJavaBridgeServerInBackground;


