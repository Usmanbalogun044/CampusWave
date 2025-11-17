# Dockerfile for wa-gateway (Node)
FROM node:18

WORKDIR /usr/src/app

COPY package*.json ./
RUN npm ci --production || npm install --production

COPY . .

ENV WA_GATEWAY_SECRET=""
EXPOSE 3000

CMD ["npm", "start"]
