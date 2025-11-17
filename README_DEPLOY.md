# Deploying Campus Wave (Laravel) + wa-gateway

This guide helps you initialize the repo and deploy both services online.

Options
- Render (recommended): easy GitHub integration, supports Docker.
- DigitalOcean App Platform: similar.
- VPS / Docker host: use `docker-compose` on your server.

Quick steps (GitHub + Render)

1. Initialize git, commit and push

```powershell
cd C:\Users\dollarhunter\Documents\github\campuswave
git init
git add .
git commit -m "initial commit: campuswave"
# Create a GitHub repo and add as remote (replace URL)
git remote add origin https://github.com/<your-username>/campuswave.git
git branch -M main
git push -u origin main
```

2. Create Render services
- Go to https://dashboard.render.com (create account)
- Create a new "Web Service" -> Connect GitHub repo -> select `campuswave` repo
- For the Laravel service, choose "Docker" and root directory `/`.
- Set build and start commands (Docker will use provided `Dockerfile`).
- Add environment variables (from your local `.env`):
  - `APP_KEY`, `APP_ENV=production`, `APP_DEBUG=false`, `WHATSAPP_API_URL`, `WHATSAPP_API_KEY`, `ADMIN_WHATSAPP_NUMBER`, `PAYMENT_ACCOUNT_NUMBER`, etc.

- Create another service for the wa-gateway:
  - Web Service -> Connect same repo, set root directory to `/wa-gateway`, choose Docker.
  - Add env var `WA_GATEWAY_SECRET` = same value as `WHATSAPP_API_KEY`.

3. After Render finishes building both services
- Visit the gateway `/status` endpoint to ensure the WhatsApp client is authenticated.
- Use your app's dashboard to accept purchases; messages will be posted to gateway.

Alternative: Deploy with Docker Compose on a server
- Copy the `docker-compose.yml` to your server, set `WHATSAPP_API_KEY` in `.env` on server, and run:

```bash
export WHATSAPP_API_KEY=your_secret_here
docker-compose up -d --build
```

Caveats & recommendations
- The wa-gateway uses WhatsApp Web (unofficial). For production reliability consider Meta Cloud API (official) or a managed provider (Twilio, 360dialog).
- Persist wa-gateway session files to avoid re-scanning QR. The container must mount a persistent volume for the session folder used by the WhatsApp library.
- Secure secrets and use HTTPS in production.

If you want, I can:
- (A) Initialize a local git commit and create the files (done), or push to a remote if you provide GitHub access (or run `gh repo create`).
- (B) Add a `Dockerfile` for a multi-stage production build and an example `nginx` config.
- (C) Create GitHub Actions that build images and push to Docker Hub (then you can connect Render to Docker Hub).

Tell me which next step you want me to run for you: init & commit locally, run `docker-compose up` locally, or help you configure Render (I can prepare exact env list and steps).