# Utiliser l'image officielle .NET 8 SDK pour builder
FROM mcr.microsoft.com/dotnet/sdk:8.0 AS build
WORKDIR /app

# Copier le fichier .csproj et restaurer les dépendances
COPY *.csproj ./
RUN dotnet restore

# Copier tout le reste et builder
COPY . ./
RUN dotnet publish -c Release -o out

# Image runtime
FROM mcr.microsoft.com/dotnet/aspnet:8.0
WORKDIR /app
COPY --from=build /app/out .

# Exposer le port
EXPOSE 8080

# Variable d'environnement pour le port
ENV ASPNETCORE_URLS=http://+:8080

# Démarrer l'application
ENTRYPOINT ["dotnet", "BrasilBurgerWeb.dll"]