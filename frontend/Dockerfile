FROM node:18

# Install Quasar CLI globally
RUN npm install -g @quasar/cli

WORKDIR /app

COPY . .
RUN npm install

EXPOSE 9000

CMD ["quasar", "dev", "--hostname", "0.0.0.0"]