# Changelog

## [0.3.0](https://github.com/ptah-sh/ptah-server/compare/v0.2.0...v0.3.0) (2024-07-04)


### Features

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try frankenphp to serve the php app and static files ([f2ecb99](https://github.com/ptah-sh/ptah-server/commit/f2ecb9984c0928b45c024e1cb8a2a18e977c9dd1))

## [0.2.0](https://github.com/ptah-sh/ptah-server/compare/v0.1.0...v0.2.0) (2024-07-04)


### Features

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try better files permissions ([230c07c](https://github.com/ptah-sh/ptah-server/commit/230c07c3386929fcadef060e820dff558822d5e5))

## [0.1.0](https://github.com/ptah-sh/ptah-server/compare/v0.0.1...v0.1.0) (2024-07-03)


### Features

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) enable pdo_pgsql ext ([a10c846](https://github.com/ptah-sh/ptah-server/commit/a10c84623291ad3a9f36d3284a5d25da719b1f6d))


### Bug Fixes

* [#38](https://github.com/ptah-sh/ptah-server/issues/38) retry service creation if the initial deployment failed ([ca5065d](https://github.com/ptah-sh/ptah-server/commit/ca5065de8cbeb396908a8255957fee3029210ab4))

## 0.0.1 (2024-07-02)


### Features

* [#1](https://github.com/ptah-sh/ptah-server/issues/1) bootstrap the laravel app ([0ebe7db](https://github.com/ptah-sh/ptah-server/commit/0ebe7db521a243b60e21aa2ebd919433ba33ac89))
* [#10](https://github.com/ptah-sh/ptah-server/issues/10) broadcast completed and failed tasks ([6a6ddfc](https://github.com/ptah-sh/ptah-server/commit/6a6ddfc2db3e40121a015e8243db751bc326a6eb))
* [#11](https://github.com/ptah-sh/ptah-server/issues/11) allow to retry tasks ([dc3f9f2](https://github.com/ptah-sh/ptah-server/commit/dc3f9f2e519f74fbe2ed47223a4ac895a79617b5))
* [#12](https://github.com/ptah-sh/ptah-server/issues/12) watch after the new agent releases ([0037a61](https://github.com/ptah-sh/ptah-server/commit/0037a61c3ac45bf1ed9e24bc9ca97c408dd492e7))
* [#14](https://github.com/ptah-sh/ptah-server/issues/14) add nodes listing to the nodex page ([44033e0](https://github.com/ptah-sh/ptah-server/commit/44033e0e4553a6fdf98fbca2e533758f8e3e9978))
* [#14](https://github.com/ptah-sh/ptah-server/issues/14) allow to configure services ([193c8ce](https://github.com/ptah-sh/ptah-server/commit/193c8cea586c6f598f97f8257811e826c573bce8))
* [#16](https://github.com/ptah-sh/ptah-server/issues/16) install caddy as a part of the initialization process ([0c87788](https://github.com/ptah-sh/ptah-server/commit/0c87788a9bb31609ba57f781b124ffcc4dfaf845))
* [#19](https://github.com/ptah-sh/ptah-server/issues/19) allow to expose services via caddy ([2842e3e](https://github.com/ptah-sh/ptah-server/commit/2842e3eef66cafb9d274375f5229034d3880859f))
* [#2](https://github.com/ptah-sh/ptah-server/issues/2) add an endpoint to receive basic info ([b213970](https://github.com/ptah-sh/ptah-server/commit/b213970abc1e1acdf33a89e28e9bcc2864216db7))
* [#20](https://github.com/ptah-sh/ptah-server/issues/20) add support for multiprocess services ([24450eb](https://github.com/ptah-sh/ptah-server/commit/24450eb6fde92260fad7ed64a20348e2668ec65e))
* [#22](https://github.com/ptah-sh/ptah-server/issues/22) add services list to the services page ([d101fd5](https://github.com/ptah-sh/ptah-server/commit/d101fd5469832962ac46b340638ba7e260f7cf23))
* [#23](https://github.com/ptah-sh/ptah-server/issues/23) allow to edit services ([60bd5a3](https://github.com/ptah-sh/ptah-server/commit/60bd5a35a25c9d8e8e3e8d25528c7b307f5af639))
* [#25](https://github.com/ptah-sh/ptah-server/issues/25) list deployments on the service's page ([fa93d62](https://github.com/ptah-sh/ptah-server/commit/fa93d62d3dabbca8c55d6c3a4d30e1e825921fc2))
* [#26](https://github.com/ptah-sh/ptah-server/issues/26) allow to delete services ([1af5d39](https://github.com/ptah-sh/ptah-server/commit/1af5d398f2fd143385a0b85044824031e24b4955))
* [#27](https://github.com/ptah-sh/ptah-server/issues/27) allow to deploy services via api ([4c1c379](https://github.com/ptah-sh/ptah-server/commit/4c1c3793307a92650aef9f9a2fd48e25944c22d2))
* [#3](https://github.com/ptah-sh/ptah-server/issues/3) create init swarm cluster task ([f993456](https://github.com/ptah-sh/ptah-server/commit/f993456590b954934f8c5e8375c4376d776751b6))
* [#31](https://github.com/ptah-sh/ptah-server/issues/31) add ptah.sh logo ([e973789](https://github.com/ptah-sh/ptah-server/commit/e9737898f4518c722d437a090d461498ac8e9c9b))
* [#34](https://github.com/ptah-sh/ptah-server/issues/34) adjust deployment endpoint to the new structure ([ee7fda4](https://github.com/ptah-sh/ptah-server/commit/ee7fda40feefa132305dff79fd9fe75eab7c48da))
* [#36](https://github.com/ptah-sh/ptah-server/issues/36) support deployment from the private registreis ([6093084](https://github.com/ptah-sh/ptah-server/commit/6093084ee724f87f4e3f435ec57b08ef699e7ba7))
* [#9](https://github.com/ptah-sh/ptah-server/issues/9) cancel next tasks in task group if the current task fails ([d36bd24](https://github.com/ptah-sh/ptah-server/commit/d36bd2461fbe165926339b092a49cf2415228524))


### Miscellaneous Chores

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) pack server into a docker image ([7b76ad1](https://github.com/ptah-sh/ptah-server/commit/7b76ad1787275581511677a09558abfb87df91be))
