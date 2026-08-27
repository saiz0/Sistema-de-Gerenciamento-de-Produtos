ARG NODE_IMAGE=node:22.23.2-alpine3.23
ARG NGINX_IMAGE=nginx:1.31.4-alpine
ARG PNPM_VERSION=11.19.0

FROM ${NODE_IMAGE} AS dependencies

ARG PNPM_VERSION

WORKDIR /app

RUN corepack enable \
    && corepack prepare pnpm@${PNPM_VERSION} --activate

COPY package.json pnpm-lock.yaml ./

RUN pnpm install --frozen-lockfile


FROM dependencies AS development

COPY . .

EXPOSE 5173

CMD ["pnpm", "dev", "--host=0.0.0.0"]


FROM dependencies AS build

ARG VITE_API_BASE_URL
ENV VITE_API_BASE_URL=${VITE_API_BASE_URL}

COPY . .

RUN pnpm build


FROM ${NGINX_IMAGE} AS production

COPY nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80
