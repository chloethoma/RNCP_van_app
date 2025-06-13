# Base
FROM node:20.18-alpine AS base

WORKDIR /app

COPY package*.json .

RUN npm install

COPY . .

# 🛠️ Dev
FROM base AS dev

EXPOSE 5173

CMD npm run dev

# 🏗️ Build for prod
FROM base AS build

RUN npm run build

# 🚀 Prod
FROM nginx:stable-alpine AS prod
COPY --from=build /app/nginx /etc/nginx/conf.d
COPY --from=build /app/dist /usr/share/nginx/html