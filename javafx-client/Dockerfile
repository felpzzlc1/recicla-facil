FROM maven:3.9-eclipse-temurin-17 AS build
WORKDIR /app
COPY pom.xml .
RUN mvn -q -B -e -DskipTests dependency:resolve
COPY src ./src
RUN mvn -q -B -DskipTests package

FROM eclipse-temurin:17-jre
ENV APP_HOME=/opt/recicla
WORKDIR ${APP_HOME}
ENV JDK_JAVA_OPTIONS="--add-opens=javafx.graphics/com.sun.javafx.tk=ALL-UNNAMED \
                      --add-opens=javafx.graphics/com.sun.javafx.tk.quantum=ALL-UNNAMED \
                      -Dprism.order=sw \
                      -Dprism.text=t2k"
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        openjfx \
        libgl1 \
        libgl1-mesa-dri \
        libgtk-3-0 \
        libxtst6 \
        libxrender1 \
        libxi6 \
        fontconfig \
        ca-certificates \
    && rm -rf /var/lib/apt/lists/*
COPY --from=build /app/target/recicla-facil-javafx-standalone.jar app.jar
ENV API_BASE_URL=http://backend/api \
    AUTH_TOKEN=
CMD ["java", "--module-path", "/usr/share/openjfx/lib", "--add-modules", "javafx.controls,javafx.fxml", "-jar", "app.jar"]

