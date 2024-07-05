# Changelog

## [0.10.5](https://github.com/ptah-sh/ptah-server/compare/v0.10.4...v0.10.5) (2024-07-05)


### Bug Fixes

* emulate prepares for postgres ([9751a84](https://github.com/ptah-sh/ptah-server/commit/9751a8476f1e5b9ecd712b0c403690eaee8b56d2))

## [0.10.4](https://github.com/ptah-sh/ptah-server/compare/v0.10.3...v0.10.4) (2024-07-05)


### Bug Fixes

* healthcheck test command ([bb379f4](https://github.com/ptah-sh/ptah-server/commit/bb379f442c96c961e81b8d98587b0825c5c54b2a))
* remove healthcheck from workers ([f7a5e2c](https://github.com/ptah-sh/ptah-server/commit/f7a5e2c3e3f1f6f6fa1fc24962355c2564fd95ad))

## [0.10.3](https://github.com/ptah-sh/ptah-server/compare/v0.10.2...v0.10.3) (2024-07-05)


### Bug Fixes

* run node auth middleware before everything else ([3602de0](https://github.com/ptah-sh/ptah-server/commit/3602de07009cad0c58b4c9aa1e4fa4fc0c557573))
* try preload team ([baa93ec](https://github.com/ptah-sh/ptah-server/commit/baa93ece5caafc30db539714071e39d7d4de6f2f))

## [0.10.2](https://github.com/ptah-sh/ptah-server/compare/v0.10.1...v0.10.2) (2024-07-05)


### Bug Fixes

* [#54](https://github.com/ptah-sh/ptah-server/issues/54) disable healthchecks for workers ([e4bfc09](https://github.com/ptah-sh/ptah-server/commit/e4bfc09e1fbc48d5f15f3c20c577c99dcef2f29a))
* authorization header for api ([d32ca14](https://github.com/ptah-sh/ptah-server/commit/d32ca14d1e164efba969da9da8aa026fa5ab4617))
* correct service id to deploy ([2c05087](https://github.com/ptah-sh/ptah-server/commit/2c05087dd1afecf855efa058188ee81d5bfa4379))
* uncomment global team scope ([0063073](https://github.com/ptah-sh/ptah-server/commit/00630732ad3bc576fdce61044e9dae8b138559e6))

## [0.10.1](https://github.com/ptah-sh/ptah-server/compare/v0.10.0...v0.10.1) (2024-07-05)


### Bug Fixes

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) remove pdo_pgsql from php.ini ([42eca94](https://github.com/ptah-sh/ptah-server/commit/42eca94ed286fd301e9c88a3884983e841029da5))

## [0.10.0](https://github.com/ptah-sh/ptah-server/compare/v0.9.0...v0.10.0) (2024-07-04)


### Features

* [#54](https://github.com/ptah-sh/ptah-server/issues/54) fix laravel-data caching ([0b8edc1](https://github.com/ptah-sh/ptah-server/commit/0b8edc16431f698c6a3054538fa68e4d984c14f3))

## [0.9.0](https://github.com/ptah-sh/ptah-server/compare/v0.8.0...v0.9.0) (2024-07-04)


### Features

* [#52](https://github.com/ptah-sh/ptah-server/issues/52) support the 'release' step ([03d7ae9](https://github.com/ptah-sh/ptah-server/commit/03d7ae934ae63b4af4aa2911b2990cddcf058d65))
* [#54](https://github.com/ptah-sh/ptah-server/issues/54) allow to add simplified workers to services ([a164d47](https://github.com/ptah-sh/ptah-server/commit/a164d476e7c021608eb3d60f9c6ce3a6385d94c2))

## [0.8.0](https://github.com/ptah-sh/ptah-server/compare/v0.7.0...v0.8.0) (2024-07-04)


### Features

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try add trusted proxies 3 ([a501e8d](https://github.com/ptah-sh/ptah-server/commit/a501e8d4d90ea75776a9f9123f2c742b6d69274d))

## [0.7.0](https://github.com/ptah-sh/ptah-server/compare/v0.6.0...v0.7.0) (2024-07-04)


### Features

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try add trusted proxies 2 ([6e0602b](https://github.com/ptah-sh/ptah-server/commit/6e0602bb48efd9dc5543a53aed94dd1377bd769a))

## [0.6.0](https://github.com/ptah-sh/ptah-server/compare/v0.5.0...v0.6.0) (2024-07-04)


### Features

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try add trusted proxies ([87a488e](https://github.com/ptah-sh/ptah-server/commit/87a488e6addb473700ee5dfddffc77d4ae5c2f8e))

## [0.5.0](https://github.com/ptah-sh/ptah-server/compare/v0.4.0...v0.5.0) (2024-07-04)


### Features

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) bundle pdo_pgsql ([6dbbe93](https://github.com/ptah-sh/ptah-server/commit/6dbbe937a41e8e67fef4fb49f52d5eb9879e1b2c))

## [0.4.0](https://github.com/ptah-sh/ptah-server/compare/v0.3.0...v0.4.0) (2024-07-04)


### Features

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try frankenphp 2 ([1f63a5a](https://github.com/ptah-sh/ptah-server/commit/1f63a5a519175b25a2d2b231ffb002424c03c643))

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
