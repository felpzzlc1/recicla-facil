FROM maven:3.9-eclipse-temurin-17 AS build
WORKDIR /app
COPY pom.xml .
RUN mvn -q -B -e -DskipTests dependency:resolve
COPY src ./src
RUN mvn -q -B -DskipTests package

FROM bellsoft/liberica-openjre-alpine:17.0.11-10
ENV APP_HOME=/opt/recicla \
    JAVA_TOOL_OPTIONS="--add-opens=javafx.graphics/com.sun.javafx.tk=ALL-UNNAMED --add-opens=javafx.graphics/com.sun.javafx.tk.quantum=ALL-UNNAMED"
WORKDIR ${APP_HOME}
RUN apk add --no-cache fontconfig ttf-dejavu
COPY --from=build /app/target/recicla-facil-javafx-standalone.jar app.jar
ENV API_BASE_URL=http://backend/api \
    AUTH_TOKEN=
CMD ["java", "--module-path", "/usr/lib/jvm/java-17-openjdk/lib", "--add-modules", "javafx.controls,javafx.fxml", "-jar", "app.jar"]

