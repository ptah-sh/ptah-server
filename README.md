# Not under active development

Docker Swarm shown itself to be a very unstable and unreliable solution for the deployment of the Ptah.sh platform.

This lead to the decision to move away from Docker Swarm and to use Kubernetes (/hand-crafted orchestrator) as the underlying technology for the Ptah.sh platform.

As this is going to be a big change, I'm archiving the project for now.

If you would like to help with the development, please file a ticket.

<p align="center"><a href="https://ptah.sh" target="_blank">
    <img src="https://github.com/ptah-sh/ptah-server/raw/main/.github/assets/logo.png" alt="Ptah.sh Logo">
</a></p>

## About Ptah.sh

Ptah.sh is a [Fair Source](https://fair.io/) self-hosting deployment platform - alternative to Heroku/Vercel and other Big Corp software. We believe that indie, startups and small to medium businesses must not suffer from unpredicted billing or bare-metal/VPS configurations.

The service is built on top of the proven container management solution - Docker Swarm.

Ptah.sh takes the pain out of deployment by easing common tasks used in many projects, such as:

-   Setting up stateful services (PostgreSQL, MongoDB, MySQL and others).
-   Scaling stateless services to an infinite number of nodes (servers, as much as Docker Swarm can do).
-   Managing automated backups for critical data.
-   Load balancing of an incoming traffic and SSL auto-provisioning via Caddy Server.
-   And many more features.

## Components

Ptah.sh is a collection of several interdependent services:

-   [Ptah.sh Server](https://github.com/ptah-sh/ptah-server) - the core of the platform, responsible for managing the infrastructure, scaling, and load balancing.
-   [Ptah.sh Agent](https://github.com/ptah-sh/ptah-agent) - the component installed on the target machine, responsible for running the containers and services.
-   [Ptah.sh Caddy](https://github.com/ptah-sh/ptah-caddy) - the component installed on the target machine, responsible for running the Caddy Server and providing metrics to the Ptah.sh Server.
-   [Ptah.sh GitHub Action](https://github.com/ptah-sh/deploy-action) - the component responsible for deploying the application to the target machine.
-   [Ptah.sh Website](https://github.com/ptah-sh/ptah-sh.github.io) - the website of the Ptah.sh platform available at [ptah.sh](https://ptah.sh), containing the documentation, 1-Click Apps templates and the public-facing information.

## Ptah.sh Sponsors

We would like to extend our thanks to the following sponsors for funding Ptah.sh development. If you are interested in becoming a sponsor, please send an e-mail to Bohdan Shulha via [contact@ptah.sh](mailto:contact@ptah.sh).

### Sponsors

-   _None so far_

#### Want to get some feature being developed faster?

Consider sponsoring the project via [GitHub Sponsors](https://github.com/sponsors/bohdan-shulha).

## Supported Operating Systems

Currently we support only the latest stable Ubuntu (24.04) with x86_64 architecture. You can build Agent and/or the server software for other operating systems, but no guarantee it will work.

In case of any trouble, please ask for help in the [community chat](https://r.ptah.sh/chat).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [CONTRIBUTING.md](https://github.com/ptah-sh/ptah-server/blob/main/CONTRIBUTING.md).

## Security Vulnerabilities

If you discover a security vulnerability within Ptah.sh services, please send an e-mail to Bohdan Shulha via [contact@ptah.sh](mailto:contact@ptah.sh). All security vulnerabilities will be promptly addressed.

## License

The Ptah.sh service suite is a Fair Source software licensed under the [Functional Source License, Version 1.1, Apache 2.0 Future License](https://github.com/ptah-sh/ptah-server/blob/main/LICENSE.md).

## Star History ★

[![Star History Chart](https://api.star-history.com/svg?repos=ptah-sh/ptah-server&type=Date)](https://star-history.com/#ptah-sh/ptah-server&Date)
