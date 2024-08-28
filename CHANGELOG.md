# Changelog

## [0.0.1](https://github.com/ptah-sh/ptah-server/compare/v0.22.8...v0.0.1) (2024-08-28)


### Features

* [#1](https://github.com/ptah-sh/ptah-server/issues/1) bootstrap the laravel app ([0ebe7db](https://github.com/ptah-sh/ptah-server/commit/0ebe7db521a243b60e21aa2ebd919433ba33ac89))
* [#10](https://github.com/ptah-sh/ptah-server/issues/10) broadcast completed and failed tasks ([6a6ddfc](https://github.com/ptah-sh/ptah-server/commit/6a6ddfc2db3e40121a015e8243db751bc326a6eb))
* [#102](https://github.com/ptah-sh/ptah-server/issues/102) try the service templates concept ([096bb40](https://github.com/ptah-sh/ptah-server/commit/096bb40ec06f67a25d29c1bbba1f21ade75c9eb4))
* [#102](https://github.com/ptah-sh/ptah-server/issues/102) use official marketplace ([fe00895](https://github.com/ptah-sh/ptah-server/commit/fe008953e0587d1fcb0dbd047149ba107abeff9d))
* [#104](https://github.com/ptah-sh/ptah-server/issues/104) adjust billing logic to re-imagined pricing ([2a37e3c](https://github.com/ptah-sh/ptah-server/commit/2a37e3ca3cca6e2a58b9c0672588de71dde923b0))
* [#11](https://github.com/ptah-sh/ptah-server/issues/11) allow to retry tasks ([dc3f9f2](https://github.com/ptah-sh/ptah-server/commit/dc3f9f2e519f74fbe2ed47223a4ac895a79617b5))
* [#114](https://github.com/ptah-sh/ptah-server/issues/114) add swarm count quotas ([859179b](https://github.com/ptah-sh/ptah-server/commit/859179b5e5c6ab25f05dbb5cd5b9816a192ebd91))
* [#12](https://github.com/ptah-sh/ptah-server/issues/12) watch after the new agent releases ([0037a61](https://github.com/ptah-sh/ptah-server/commit/0037a61c3ac45bf1ed9e24bc9ca97c408dd492e7))
* [#122](https://github.com/ptah-sh/ptah-server/issues/122) rework swarm-related management interfaces ([16bc52c](https://github.com/ptah-sh/ptah-server/commit/16bc52ca0eadd665bfa39fb1733513a1027cc9da))
* [#124](https://github.com/ptah-sh/ptah-server/issues/124) add sentry ingegration ([716d2b3](https://github.com/ptah-sh/ptah-server/commit/716d2b314cf1be381746883cc5cd23ee5bae94fe))
* [#14](https://github.com/ptah-sh/ptah-server/issues/14) add nodes listing to the nodex page ([44033e0](https://github.com/ptah-sh/ptah-server/commit/44033e0e4553a6fdf98fbca2e533758f8e3e9978))
* [#14](https://github.com/ptah-sh/ptah-server/issues/14) allow to configure services ([193c8ce](https://github.com/ptah-sh/ptah-server/commit/193c8cea586c6f598f97f8257811e826c573bce8))
* [#140](https://github.com/ptah-sh/ptah-server/issues/140) use slugs as the service identifiers ([3860f24](https://github.com/ptah-sh/ptah-server/commit/3860f245ba2b598badf7a87ca6a91e69b3776106))
* [#142](https://github.com/ptah-sh/ptah-server/issues/142) use gh action to deploy to ptah.sh ([48e31ac](https://github.com/ptah-sh/ptah-server/commit/48e31ac48447898b7caa4f46297d24fc3edc903b))
* [#145](https://github.com/ptah-sh/ptah-server/issues/145) rework 1-click template forms logic ([b2819e5](https://github.com/ptah-sh/ptah-server/commit/b2819e5ef614589ab84c0bd8dcf52cb973ac87df))
* [#16](https://github.com/ptah-sh/ptah-server/issues/16) install caddy as a part of the initialization process ([0c87788](https://github.com/ptah-sh/ptah-server/commit/0c87788a9bb31609ba57f781b124ffcc4dfaf845))
* [#19](https://github.com/ptah-sh/ptah-server/issues/19) allow to expose services via caddy ([2842e3e](https://github.com/ptah-sh/ptah-server/commit/2842e3eef66cafb9d274375f5229034d3880859f))
* [#2](https://github.com/ptah-sh/ptah-server/issues/2) add an endpoint to receive basic info ([b213970](https://github.com/ptah-sh/ptah-server/commit/b213970abc1e1acdf33a89e28e9bcc2864216db7))
* [#20](https://github.com/ptah-sh/ptah-server/issues/20) add support for multiprocess services ([24450eb](https://github.com/ptah-sh/ptah-server/commit/24450eb6fde92260fad7ed64a20348e2668ec65e))
* [#22](https://github.com/ptah-sh/ptah-server/issues/22) add services list to the services page ([d101fd5](https://github.com/ptah-sh/ptah-server/commit/d101fd5469832962ac46b340638ba7e260f7cf23))
* [#23](https://github.com/ptah-sh/ptah-server/issues/23) allow to edit services ([60bd5a3](https://github.com/ptah-sh/ptah-server/commit/60bd5a35a25c9d8e8e3e8d25528c7b307f5af639))
* [#25](https://github.com/ptah-sh/ptah-server/issues/25) list deployments on the service's page ([fa93d62](https://github.com/ptah-sh/ptah-server/commit/fa93d62d3dabbca8c55d6c3a4d30e1e825921fc2))
* [#26](https://github.com/ptah-sh/ptah-server/issues/26) allow to delete services ([1af5d39](https://github.com/ptah-sh/ptah-server/commit/1af5d398f2fd143385a0b85044824031e24b4955))
* [#27](https://github.com/ptah-sh/ptah-server/issues/27) allow to deploy services via api ([4c1c379](https://github.com/ptah-sh/ptah-server/commit/4c1c3793307a92650aef9f9a2fd48e25944c22d2))
* [#28](https://github.com/ptah-sh/ptah-server/issues/28) allow to add redirect rules ([212b6e0](https://github.com/ptah-sh/ptah-server/commit/212b6e0d0f100838b046715b8cc6dcd70841c474))
* [#3](https://github.com/ptah-sh/ptah-server/issues/3) create init swarm cluster task ([f993456](https://github.com/ptah-sh/ptah-server/commit/f993456590b954934f8c5e8375c4376d776751b6))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) bundle pdo_pgsql ([6dbbe93](https://github.com/ptah-sh/ptah-server/commit/6dbbe937a41e8e67fef4fb49f52d5eb9879e1b2c))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) enable pdo_pgsql ext ([a10c846](https://github.com/ptah-sh/ptah-server/commit/a10c84623291ad3a9f36d3284a5d25da719b1f6d))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try add trusted proxies ([87a488e](https://github.com/ptah-sh/ptah-server/commit/87a488e6addb473700ee5dfddffc77d4ae5c2f8e))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try add trusted proxies 2 ([6e0602b](https://github.com/ptah-sh/ptah-server/commit/6e0602bb48efd9dc5543a53aed94dd1377bd769a))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try add trusted proxies 3 ([a501e8d](https://github.com/ptah-sh/ptah-server/commit/a501e8d4d90ea75776a9f9123f2c742b6d69274d))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try better files permissions ([230c07c](https://github.com/ptah-sh/ptah-server/commit/230c07c3386929fcadef060e820dff558822d5e5))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try frankenphp 2 ([1f63a5a](https://github.com/ptah-sh/ptah-server/commit/1f63a5a519175b25a2d2b231ffb002424c03c643))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) try frankenphp to serve the php app and static files ([f2ecb99](https://github.com/ptah-sh/ptah-server/commit/f2ecb9984c0928b45c024e1cb8a2a18e977c9dd1))
* [#31](https://github.com/ptah-sh/ptah-server/issues/31) add ptah.sh logo ([e973789](https://github.com/ptah-sh/ptah-server/commit/e9737898f4518c722d437a090d461498ac8e9c9b))
* [#34](https://github.com/ptah-sh/ptah-server/issues/34) adjust deployment endpoint to the new structure ([ee7fda4](https://github.com/ptah-sh/ptah-server/commit/ee7fda40feefa132305dff79fd9fe75eab7c48da))
* [#35](https://github.com/ptah-sh/ptah-server/issues/35) add 404 handler to caddy ([ee773d9](https://github.com/ptah-sh/ptah-server/commit/ee773d91ad468ce743c035305312cdb1c3dc3946))
* [#36](https://github.com/ptah-sh/ptah-server/issues/36) support deployment from the private registreis ([6093084](https://github.com/ptah-sh/ptah-server/commit/6093084ee724f87f4e3f435ec57b08ef699e7ba7))
* [#52](https://github.com/ptah-sh/ptah-server/issues/52) support the 'release' step ([03d7ae9](https://github.com/ptah-sh/ptah-server/commit/03d7ae934ae63b4af4aa2911b2990cddcf058d65))
* [#54](https://github.com/ptah-sh/ptah-server/issues/54) allow to add simplified workers to services ([a164d47](https://github.com/ptah-sh/ptah-server/commit/a164d476e7c021608eb3d60f9c6ce3a6385d94c2))
* [#54](https://github.com/ptah-sh/ptah-server/issues/54) fix laravel-data caching ([0b8edc1](https://github.com/ptah-sh/ptah-server/commit/0b8edc16431f698c6a3054538fa68e4d984c14f3))
* [#63](https://github.com/ptah-sh/ptah-server/issues/63) allow to manage storages for large files ([487ace4](https://github.com/ptah-sh/ptah-server/commit/487ace41a4e03ff4d28d7b73795b5af47fa253f4))
* [#64](https://github.com/ptah-sh/ptah-server/issues/64) allow to run arbitrary backup commands for processes ([698905f](https://github.com/ptah-sh/ptah-server/commit/698905f3076e6d5f39fcc818f68172f1ccd119e3))
* [#65](https://github.com/ptah-sh/ptah-server/issues/65) allow to backup volumes ([3aba5f1](https://github.com/ptah-sh/ptah-server/commit/3aba5f12fca034be82425124bcf0c01591ac91c1))
* [#68](https://github.com/ptah-sh/ptah-server/issues/68) replace jetstream dashboard with saas-related microdashboard ([bc46fef](https://github.com/ptah-sh/ptah-server/commit/bc46fef19a34afff16485e39b7b5b2e05d87f21f))
* [#75](https://github.com/ptah-sh/ptah-server/issues/75) prevent full service shutdown during update ([16fdf9b](https://github.com/ptah-sh/ptah-server/commit/16fdf9b8e03fb46d45f4ed840bc797c9410822e1))
* [#78](https://github.com/ptah-sh/ptah-server/issues/78) add bcmath ([0884fc9](https://github.com/ptah-sh/ptah-server/commit/0884fc9c2ce2e91cd1f3fe621500a9a63ae42990))
* [#78](https://github.com/ptah-sh/ptah-server/issues/78) add subscriptions with Paddle ([ad0586b](https://github.com/ptah-sh/ptah-server/commit/ad0586b85072d540e58433919f2224b7b83c9310))
* [#79](https://github.com/ptah-sh/ptah-server/issues/79) enable e-mail verification ([d836bb1](https://github.com/ptah-sh/ptah-server/commit/d836bb1dc2e891af8c7964395d51567ef5e711b0))
* [#81](https://github.com/ptah-sh/ptah-server/issues/81) send e-mail notifications regarding ending trial ([00ec406](https://github.com/ptah-sh/ptah-server/commit/00ec40613cf2dad5bf001f14c517f7cfea04bc33))
* [#82](https://github.com/ptah-sh/ptah-server/issues/82) restrict access to the service when a user doesn't have an active subscription ([012785c](https://github.com/ptah-sh/ptah-server/commit/012785cb8997b9a1bf20559d5b669a1c579c9580))
* [#83](https://github.com/ptah-sh/ptah-server/issues/83) allow a node to join an existing cluster ([373e15d](https://github.com/ptah-sh/ptah-server/commit/373e15d82ffab531e5f3fe943fc16a645f4d6931))
* [#9](https://github.com/ptah-sh/ptah-server/issues/9) cancel next tasks in task group if the current task fails ([d36bd24](https://github.com/ptah-sh/ptah-server/commit/d36bd2461fbe165926339b092a49cf2415228524))


### Bug Fixes

* [#107](https://github.com/ptah-sh/ptah-server/issues/107) set default join tokens ([2e1790f](https://github.com/ptah-sh/ptah-server/commit/2e1790f10540e44f7e01cbfbed9f9327a610b09f))
* [#108](https://github.com/ptah-sh/ptah-server/issues/108) create secrets file each deployment ([9e1cdf1](https://github.com/ptah-sh/ptah-server/commit/9e1cdf14063b83d8b7552f65cb5ba5fad43a3939))
* [#108](https://github.com/ptah-sh/ptah-server/issues/108) workers secrets config creation ([47e80f2](https://github.com/ptah-sh/ptah-server/commit/47e80f266a3cb9a1b0d63958e69a164559ee86ba))
* [#112](https://github.com/ptah-sh/ptah-server/issues/112) run scheduled commands on server but not on other cli commands ([f716062](https://github.com/ptah-sh/ptah-server/commit/f7160620262857be7b212614d4d28ef85a543fbd))
* [#116](https://github.com/ptah-sh/ptah-server/issues/116) inertia integration ([7b42be7](https://github.com/ptah-sh/ptah-server/commit/7b42be793885461141c9173b25ff89558b993449))
* [#116](https://github.com/ptah-sh/ptah-server/issues/116) remove services (processes) when a service has been deleted from ui ([07da9de](https://github.com/ptah-sh/ptah-server/commit/07da9de0d72111bb0a6f78be49dbae64c8d71b05))
* [#124](https://github.com/ptah-sh/ptah-server/issues/124) try installing excimer via pecl ([67e5616](https://github.com/ptah-sh/ptah-server/commit/67e56160a6d71fffa9c4c2a8f8e7aa9a56aa5a59))
* [#133](https://github.com/ptah-sh/ptah-server/issues/133) display a subscription warning banner ([aa02cc7](https://github.com/ptah-sh/ptah-server/commit/aa02cc7ed8a3eda9512b5e09519a1eb263b92790))
* [#138](https://github.com/ptah-sh/ptah-server/issues/138) use dird to get the real ip of end-users ([6ca2ccc](https://github.com/ptah-sh/ptah-server/commit/6ca2ccc317096e95766acbb638e59724a2f23a69))
* [#142](https://github.com/ptah-sh/ptah-server/issues/142) clear extends on template selection ([2be0006](https://github.com/ptah-sh/ptah-server/commit/2be0006e711ff1f6160153cd5b1b694d857d65de))
* [#142](https://github.com/ptah-sh/ptah-server/issues/142) gh action version ([d25c58d](https://github.com/ptah-sh/ptah-server/commit/d25c58d03cf78dc61f8728bf75501b146bb3f277))
* [#142](https://github.com/ptah-sh/ptah-server/issues/142) ooops apitoken not api key ([7b15f1d](https://github.com/ptah-sh/ptah-server/commit/7b15f1d270db5afa4582a6b149a29884789e1acd))
* [#142](https://github.com/ptah-sh/ptah-server/issues/142) sentry release, not sentry version ([a449b61](https://github.com/ptah-sh/ptah-server/commit/a449b61bb8962d64eeebdf03521dad8e499798de))
* [#143](https://github.com/ptah-sh/ptah-server/issues/143) omit caddy configs for deleted services ([508b935](https://github.com/ptah-sh/ptah-server/commit/508b9352e8c692ef137930cd128f66ba45d1da19))
* [#151](https://github.com/ptah-sh/ptah-server/issues/151), [#152](https://github.com/ptah-sh/ptah-server/issues/152), [#118](https://github.com/ptah-sh/ptah-server/issues/118) correct security measures ([d693784](https://github.com/ptah-sh/ptah-server/commit/d693784a6d37de493f84959966ca183bfb50aed0))
* [#27](https://github.com/ptah-sh/ptah-server/issues/27) access all available teams' resources via api ([9dc3616](https://github.com/ptah-sh/ptah-server/commit/9dc3616b876f364ebc69c5c6ee49d258b4467da0))
* [#28](https://github.com/ptah-sh/ptah-server/issues/28) re-order rules ([aa57f3b](https://github.com/ptah-sh/ptah-server/commit/aa57f3b60b1216c537a52ddcd415677794a41a2a))
* [#30](https://github.com/ptah-sh/ptah-server/issues/30) remove pdo_pgsql from php.ini ([42eca94](https://github.com/ptah-sh/ptah-server/commit/42eca94ed286fd301e9c88a3884983e841029da5))
* [#38](https://github.com/ptah-sh/ptah-server/issues/38) retry service creation if the initial deployment failed ([ca5065d](https://github.com/ptah-sh/ptah-server/commit/ca5065de8cbeb396908a8255957fee3029210ab4))
* [#54](https://github.com/ptah-sh/ptah-server/issues/54) disable healthchecks for workers ([e4bfc09](https://github.com/ptah-sh/ptah-server/commit/e4bfc09e1fbc48d5f15f3c20c577c99dcef2f29a))
* [#72](https://github.com/ptah-sh/ptah-server/issues/72) assign correct team when the service is accessed via api ([0e7cfe4](https://github.com/ptah-sh/ptah-server/commit/0e7cfe4de6d2e2ca2f825e6d254fb7af6a85894f))
* [#74](https://github.com/ptah-sh/ptah-server/issues/74) de-duplicate updated env vars ([22182f8](https://github.com/ptah-sh/ptah-server/commit/22182f8f46d021a4999618d49a34352da9c4ca83))
* [#77](https://github.com/ptah-sh/ptah-server/issues/77) broken docker registry link in the service if the registry is renamed ([ce811ef](https://github.com/ptah-sh/ptah-server/commit/ce811efd4d352743ded723db6b9a0e1c6cd02eb1))
* [#78](https://github.com/ptah-sh/ptah-server/issues/78) dont ask db for jobs if running in console ([de00336](https://github.com/ptah-sh/ptah-server/commit/de00336e12ea8329b0bd6f061ca3f083833b11e4))
* [#78](https://github.com/ptah-sh/ptah-server/issues/78) fix subscription middleware logic ([53645d9](https://github.com/ptah-sh/ptah-server/commit/53645d9de33c0450d1bf893da2781fdd92b2c764))
* [#78](https://github.com/ptah-sh/ptah-server/issues/78) fix trial management ([5c6cba1](https://github.com/ptah-sh/ptah-server/commit/5c6cba19ae5aaefb8f397a89f640338f91554cf8))
* [#94](https://github.com/ptah-sh/ptah-server/issues/94) require billing name and email on team creation ([a57f2b1](https://github.com/ptah-sh/ptah-server/commit/a57f2b1345e8783edc3b90d59aef0375a70db3e8))
* [#96](https://github.com/ptah-sh/ptah-server/issues/96) cancel subscription on team deletion ([543b69b](https://github.com/ptah-sh/ptah-server/commit/543b69b5dd3f02e4d0e5710afad5b1c4eade6690))
* add resend package ([d666c06](https://github.com/ptah-sh/ptah-server/commit/d666c06d6a568d0c8f445f55529e35e69b728bf3))
* authorization header for api ([d32ca14](https://github.com/ptah-sh/ptah-server/commit/d32ca14d1e164efba969da9da8aa026fa5ab4617))
* correct service id to deploy ([2c05087](https://github.com/ptah-sh/ptah-server/commit/2c05087dd1afecf855efa058188ee81d5bfa4379))
* correct subscription class ([029da3c](https://github.com/ptah-sh/ptah-server/commit/029da3c01a9f004225bc3fcf5d3fac533a9218b3))
* emulate prepares for postgres ([bad4e73](https://github.com/ptah-sh/ptah-server/commit/bad4e73a5e1283f7526fe4fc91fa63d31f33a11a))
* emulate prepares for postgres ([9751a84](https://github.com/ptah-sh/ptah-server/commit/9751a8476f1e5b9ecd712b0c403690eaee8b56d2))
* expired subscriptions can't be managed on paddle ([396b5ca](https://github.com/ptah-sh/ptah-server/commit/396b5ca86843c2f161861638e070f4bf5e5fb4bd))
* healthcheck test command ([bb379f4](https://github.com/ptah-sh/ptah-server/commit/bb379f442c96c961e81b8d98587b0825c5c54b2a))
* pricing on the billing page ([bae520b](https://github.com/ptah-sh/ptah-server/commit/bae520b518e0e1d36a8fc9cc24a8821b53caa531))
* remove healthcheck from workers ([f7a5e2c](https://github.com/ptah-sh/ptah-server/commit/f7a5e2c3e3f1f6f6fa1fc24962355c2564fd95ad))
* run node auth middleware before everything else ([3602de0](https://github.com/ptah-sh/ptah-server/commit/3602de07009cad0c58b4c9aa1e4fa4fc0c557573))
* schedule ([e46834b](https://github.com/ptah-sh/ptah-server/commit/e46834b535daf97350ca563753b7648fd1537b9b))
* try preload team ([baa93ec](https://github.com/ptah-sh/ptah-server/commit/baa93ece5caafc30db539714071e39d7d4de6f2f))
* uncomment global team scope ([0063073](https://github.com/ptah-sh/ptah-server/commit/00630732ad3bc576fdce61044e9dae8b138559e6))


### Miscellaneous Chores

* [#30](https://github.com/ptah-sh/ptah-server/issues/30) pack server into a docker image ([7b76ad1](https://github.com/ptah-sh/ptah-server/commit/7b76ad1787275581511677a09558abfb87df91be))
* [#4](https://github.com/ptah-sh/ptah-server/issues/4) add ci step ([368f484](https://github.com/ptah-sh/ptah-server/commit/368f48467080847e9040150885dd8d62e218369d))
* [#85](https://github.com/ptah-sh/ptah-server/issues/85) add legal links ([e7290e9](https://github.com/ptah-sh/ptah-server/commit/e7290e9f8c16197c587d7bef50d47b71ff082c8e))
* add os notice to readme ([524aaf9](https://github.com/ptah-sh/ptah-server/commit/524aaf9db04114cfe7292e34818051e2a70ed80e))
* add ptah.sh logo to readme ([3186bf9](https://github.com/ptah-sh/ptah-server/commit/3186bf9f45c14e9e11876137452e3ab27b613ad7))
* allow contributions ([8bf83d0](https://github.com/ptah-sh/ptah-server/commit/8bf83d0d5d932a457a8d46b82a123889e8eb034a))
* fix open source to fair source ([bd8e23f](https://github.com/ptah-sh/ptah-server/commit/bd8e23f3c0ef25427415e484f957d3bcaf0dbda3))
* fix pricing to reflect the latest changes ([bb5869b](https://github.com/ptah-sh/ptah-server/commit/bb5869b7504ddb5beb02f0b8ffbb68bc7674c65b))
* **main:** release 0.0.1 ([f4b373d](https://github.com/ptah-sh/ptah-server/commit/f4b373ddbdcd13432bea2f448ba8ecd5fa748781))
* **main:** release 0.1.0 ([35fb739](https://github.com/ptah-sh/ptah-server/commit/35fb73959680ec697233bd47ac309d5f418c5859))
* **main:** release 0.10.0 ([e7c9c8e](https://github.com/ptah-sh/ptah-server/commit/e7c9c8e20706e489c2a01a130689a26575238bcd))
* **main:** release 0.10.1 ([1ec04e2](https://github.com/ptah-sh/ptah-server/commit/1ec04e24d96a176a99ff9f80275124791c905ee9))
* **main:** release 0.10.2 ([1f5d2e5](https://github.com/ptah-sh/ptah-server/commit/1f5d2e5df69a034140db5994ecbb07ec9ab8e3e6))
* **main:** release 0.10.3 ([55a4d8d](https://github.com/ptah-sh/ptah-server/commit/55a4d8d8eca0e9b29d4a91a597d25bedbeedd35e))
* **main:** release 0.10.4 ([508e3b9](https://github.com/ptah-sh/ptah-server/commit/508e3b9863d7bf4baade46cdf62f3e0caf8bb924))
* **main:** release 0.10.5 ([984cd6b](https://github.com/ptah-sh/ptah-server/commit/984cd6b30b7729bb28ab51b8e9c775d73726360f))
* **main:** release 0.11.0 ([f160106](https://github.com/ptah-sh/ptah-server/commit/f160106eed26780156467d2b9d1b2da46f1670e9))
* **main:** release 0.11.1 ([fd85362](https://github.com/ptah-sh/ptah-server/commit/fd85362b7168d7b832c71d215e5b0212d47f9aeb))
* **main:** release 0.11.2 ([bc2d502](https://github.com/ptah-sh/ptah-server/commit/bc2d5025db62c332d96ad831f4898da39006da00))
* **main:** release 0.11.3 ([33d4b88](https://github.com/ptah-sh/ptah-server/commit/33d4b88fab019460e44f5a86fb7d40c097f796bb))
* **main:** release 0.12.0 ([883654c](https://github.com/ptah-sh/ptah-server/commit/883654c580489512053599b4a1f41f201a5a4013))
* **main:** release 0.13.0 ([64ffc09](https://github.com/ptah-sh/ptah-server/commit/64ffc0908c0c98fad09f2b58cba1d87a9c7efc00))
* **main:** release 0.13.1 ([d8a436f](https://github.com/ptah-sh/ptah-server/commit/d8a436f06258612b75e6d60eae88aebb74ef48eb))
* **main:** release 0.13.2 ([33b38f1](https://github.com/ptah-sh/ptah-server/commit/33b38f1faef1d3d3ef0d62d2d48db1258845662f))
* **main:** release 0.13.3 ([b330b62](https://github.com/ptah-sh/ptah-server/commit/b330b62f5d24f094fbdde2e3f5e392a49a678399))
* **main:** release 0.13.4 ([59b5d2c](https://github.com/ptah-sh/ptah-server/commit/59b5d2c17cf1cb99bb07e8acdec6dae72c46db5d))
* **main:** release 0.13.5 ([e69f7bb](https://github.com/ptah-sh/ptah-server/commit/e69f7bb5a1c713dcdec20f0047dfe97f749a1af8))
* **main:** release 0.14.0 ([700b0d0](https://github.com/ptah-sh/ptah-server/commit/700b0d08ccf73a06a7dddd0319b9ca41328a0210))
* **main:** release 0.15.0 ([a6259df](https://github.com/ptah-sh/ptah-server/commit/a6259df34e839a1c3e1c032fa46091af3be3005d))
* **main:** release 0.15.1 ([2b7981e](https://github.com/ptah-sh/ptah-server/commit/2b7981e37151cd6ea632619ce0613198c1cc8102))
* **main:** release 0.15.2 ([a919cb6](https://github.com/ptah-sh/ptah-server/commit/a919cb6b49aff15fc3c130075ba3cfc0c7801516))
* **main:** release 0.15.3 ([1d7188d](https://github.com/ptah-sh/ptah-server/commit/1d7188d63383e4e0a9d6aa443967788e53232e08))
* **main:** release 0.15.4 ([56bf926](https://github.com/ptah-sh/ptah-server/commit/56bf926a92354dc8728ea64b05a94981d2824b58))
* **main:** release 0.15.5 ([23261f4](https://github.com/ptah-sh/ptah-server/commit/23261f41c99408122fdc611e7581d8c8b5ba51f5))
* **main:** release 0.16.0 ([8728988](https://github.com/ptah-sh/ptah-server/commit/87289887dc13da928c5962906a471adbe7384cee))
* **main:** release 0.17.0 ([2d75afa](https://github.com/ptah-sh/ptah-server/commit/2d75afa5614c84e79a082418a46682bd3cb2f38d))
* **main:** release 0.17.1 ([bf41ef6](https://github.com/ptah-sh/ptah-server/commit/bf41ef60aa3de8973c7e03148cfba5d8e18074b9))
* **main:** release 0.18.0 ([46d62cb](https://github.com/ptah-sh/ptah-server/commit/46d62cb57b74f1f5ffc8009553a3d48fee750748))
* **main:** release 0.19.0 ([65e3f93](https://github.com/ptah-sh/ptah-server/commit/65e3f93dd562a1a48c0a3a043a339e53d129e8dd))
* **main:** release 0.19.1 ([2ab5c85](https://github.com/ptah-sh/ptah-server/commit/2ab5c85bc1305afb56e4fb1678536f3f52324de1))
* **main:** release 0.2.0 ([fff2294](https://github.com/ptah-sh/ptah-server/commit/fff229487b4a5848694cbac98aeaf2a54da3fe82))
* **main:** release 0.20.0 ([3432c65](https://github.com/ptah-sh/ptah-server/commit/3432c65c408edff8f592ca991694cc601c9a4ddc))
* **main:** release 0.20.1 ([6630902](https://github.com/ptah-sh/ptah-server/commit/6630902eb291259c4d3a89d4ce51be12e2eb6b06))
* **main:** release 0.21.0 ([a1c03e3](https://github.com/ptah-sh/ptah-server/commit/a1c03e3134b9fccc32f2a8cb02bce034f39b10d3))
* **main:** release 0.22.0 ([267c41f](https://github.com/ptah-sh/ptah-server/commit/267c41f72f41066d4883a050f2480b5a29248270))
* **main:** release 0.22.1 ([b11584d](https://github.com/ptah-sh/ptah-server/commit/b11584dd078ed2a44cfee0ff61428f7e5ef33cb8))
* **main:** release 0.22.2 ([0fa7752](https://github.com/ptah-sh/ptah-server/commit/0fa775208e0d24a458e6f517370f54b6522bfc8f))
* **main:** release 0.22.3 ([0dd8656](https://github.com/ptah-sh/ptah-server/commit/0dd86568d692ba769b735b8e30bbff055acb93a4))
* **main:** release 0.22.4 ([c2b8f6a](https://github.com/ptah-sh/ptah-server/commit/c2b8f6a9593122483009491bd55a401e9bd82c9a))
* **main:** release 0.22.5 ([7a530ea](https://github.com/ptah-sh/ptah-server/commit/7a530ea7b1688c11b54db6ea73b312232ae89202))
* **main:** release 0.22.6 ([ee1644e](https://github.com/ptah-sh/ptah-server/commit/ee1644eadfb1ae722550e7befdc37fde5286699b))
* **main:** release 0.22.7 ([4cb625f](https://github.com/ptah-sh/ptah-server/commit/4cb625ff4c39ec5998a1c8bae437710c11c2d8aa))
* **main:** release 0.22.8 ([0b93bc0](https://github.com/ptah-sh/ptah-server/commit/0b93bc075554681c48ddb34f1b9a9ad19aae36d3))
* **main:** release 0.3.0 ([aa79dd8](https://github.com/ptah-sh/ptah-server/commit/aa79dd8c83a69e3b0538872638188443a4e97afa))
* **main:** release 0.4.0 ([e600d0f](https://github.com/ptah-sh/ptah-server/commit/e600d0f9ff19c2f495c8e7a5d9e2a753684361aa))
* **main:** release 0.5.0 ([3a6c146](https://github.com/ptah-sh/ptah-server/commit/3a6c146a245f32684959da52bf81190186561401))
* **main:** release 0.6.0 ([b417377](https://github.com/ptah-sh/ptah-server/commit/b4173774e5f3d3cee65221d986fea9de5ccceecd))
* **main:** release 0.7.0 ([2f70e5d](https://github.com/ptah-sh/ptah-server/commit/2f70e5da9a0c2c690e6df504f22248380ccceda7))
* **main:** release 0.8.0 ([ae6b490](https://github.com/ptah-sh/ptah-server/commit/ae6b49040e7981df85616385a13dc084112535f9))
* **main:** release 0.9.0 ([4528401](https://github.com/ptah-sh/ptah-server/commit/45284014f522fa0b6f72bd83ae6c377f064f6523))

## [0.22.8](https://github.com/ptah-sh/ptah-server/compare/v0.22.7...v0.22.8) (2024-08-28)


### Bug Fixes

* correct subscription class ([029da3c](https://github.com/ptah-sh/ptah-server/commit/029da3c01a9f004225bc3fcf5d3fac533a9218b3))

## [0.22.7](https://github.com/ptah-sh/ptah-server/compare/v0.22.6...v0.22.7) (2024-08-28)


### Bug Fixes

* pricing on the billing page ([bae520b](https://github.com/ptah-sh/ptah-server/commit/bae520b518e0e1d36a8fc9cc24a8821b53caa531))

## [0.22.6](https://github.com/ptah-sh/ptah-server/compare/v0.22.5...v0.22.6) (2024-08-28)


### Bug Fixes

* [#151](https://github.com/ptah-sh/ptah-server/issues/151), [#152](https://github.com/ptah-sh/ptah-server/issues/152), [#118](https://github.com/ptah-sh/ptah-server/issues/118) correct security measures ([d693784](https://github.com/ptah-sh/ptah-server/commit/d693784a6d37de493f84959966ca183bfb50aed0))

## [0.22.5](https://github.com/ptah-sh/ptah-server/compare/v0.22.4...v0.22.5) (2024-08-28)


### Bug Fixes

* expired subscriptions can't be managed on paddle ([396b5ca](https://github.com/ptah-sh/ptah-server/commit/396b5ca86843c2f161861638e070f4bf5e5fb4bd))


### Miscellaneous Chores

* allow contributions ([8bf83d0](https://github.com/ptah-sh/ptah-server/commit/8bf83d0d5d932a457a8d46b82a123889e8eb034a))

## [0.22.4](https://github.com/ptah-sh/ptah-server/compare/v0.22.3...v0.22.4) (2024-08-27)


### Bug Fixes

* [#142](https://github.com/ptah-sh/ptah-server/issues/142) sentry release, not sentry version ([a449b61](https://github.com/ptah-sh/ptah-server/commit/a449b61bb8962d64eeebdf03521dad8e499798de))

## [0.22.3](https://github.com/ptah-sh/ptah-server/compare/v0.22.2...v0.22.3) (2024-08-27)


### Bug Fixes

* [#142](https://github.com/ptah-sh/ptah-server/issues/142) clear extends on template selection ([2be0006](https://github.com/ptah-sh/ptah-server/commit/2be0006e711ff1f6160153cd5b1b694d857d65de))

## [0.22.2](https://github.com/ptah-sh/ptah-server/compare/v0.22.1...v0.22.2) (2024-08-27)


### Bug Fixes

* [#142](https://github.com/ptah-sh/ptah-server/issues/142) ooops apitoken not api key ([7b15f1d](https://github.com/ptah-sh/ptah-server/commit/7b15f1d270db5afa4582a6b149a29884789e1acd))

## [0.22.1](https://github.com/ptah-sh/ptah-server/compare/v0.22.0...v0.22.1) (2024-08-27)


### Bug Fixes

* [#142](https://github.com/ptah-sh/ptah-server/issues/142) gh action version ([d25c58d](https://github.com/ptah-sh/ptah-server/commit/d25c58d03cf78dc61f8728bf75501b146bb3f277))

## [0.22.0](https://github.com/ptah-sh/ptah-server/compare/v0.21.0...v0.22.0) (2024-08-27)


### Features

* [#142](https://github.com/ptah-sh/ptah-server/issues/142) use gh action to deploy to ptah.sh ([48e31ac](https://github.com/ptah-sh/ptah-server/commit/48e31ac48447898b7caa4f46297d24fc3edc903b))
* [#145](https://github.com/ptah-sh/ptah-server/issues/145) rework 1-click template forms logic ([b2819e5](https://github.com/ptah-sh/ptah-server/commit/b2819e5ef614589ab84c0bd8dcf52cb973ac87df))


### Bug Fixes

* [#143](https://github.com/ptah-sh/ptah-server/issues/143) omit caddy configs for deleted services ([508b935](https://github.com/ptah-sh/ptah-server/commit/508b9352e8c692ef137930cd128f66ba45d1da19))

## [0.21.0](https://github.com/ptah-sh/ptah-server/compare/v0.20.1...v0.21.0) (2024-08-25)


### Features

* [#140](https://github.com/ptah-sh/ptah-server/issues/140) use slugs as the service identifiers ([3860f24](https://github.com/ptah-sh/ptah-server/commit/3860f245ba2b598badf7a87ca6a91e69b3776106))

## [0.20.1](https://github.com/ptah-sh/ptah-server/compare/v0.20.0...v0.20.1) (2024-08-25)


### Bug Fixes

* [#138](https://github.com/ptah-sh/ptah-server/issues/138) use dird to get the real ip of end-users ([6ca2ccc](https://github.com/ptah-sh/ptah-server/commit/6ca2ccc317096e95766acbb638e59724a2f23a69))


### Miscellaneous Chores

* add os notice to readme ([524aaf9](https://github.com/ptah-sh/ptah-server/commit/524aaf9db04114cfe7292e34818051e2a70ed80e))

## [0.20.0](https://github.com/ptah-sh/ptah-server/compare/v0.19.1...v0.20.0) (2024-08-20)


### Features

* [#122](https://github.com/ptah-sh/ptah-server/issues/122) rework swarm-related management interfaces ([16bc52c](https://github.com/ptah-sh/ptah-server/commit/16bc52ca0eadd665bfa39fb1733513a1027cc9da))

## [0.19.1](https://github.com/ptah-sh/ptah-server/compare/v0.19.0...v0.19.1) (2024-08-20)


### Bug Fixes

* [#133](https://github.com/ptah-sh/ptah-server/issues/133) display a subscription warning banner ([aa02cc7](https://github.com/ptah-sh/ptah-server/commit/aa02cc7ed8a3eda9512b5e09519a1eb263b92790))
* schedule ([e46834b](https://github.com/ptah-sh/ptah-server/commit/e46834b535daf97350ca563753b7648fd1537b9b))

## [0.19.0](https://github.com/ptah-sh/ptah-server/compare/v0.18.0...v0.19.0) (2024-08-19)


### Features

* [#102](https://github.com/ptah-sh/ptah-server/issues/102) use official marketplace ([fe00895](https://github.com/ptah-sh/ptah-server/commit/fe008953e0587d1fcb0dbd047149ba107abeff9d))

## [0.18.0](https://github.com/ptah-sh/ptah-server/compare/v0.17.1...v0.18.0) (2024-08-19)


### Features

* [#102](https://github.com/ptah-sh/ptah-server/issues/102) try the service templates concept ([096bb40](https://github.com/ptah-sh/ptah-server/commit/096bb40ec06f67a25d29c1bbba1f21ade75c9eb4))

## [0.17.1](https://github.com/ptah-sh/ptah-server/compare/v0.17.0...v0.17.1) (2024-08-16)


### Bug Fixes

* [#124](https://github.com/ptah-sh/ptah-server/issues/124) try installing excimer via pecl ([67e5616](https://github.com/ptah-sh/ptah-server/commit/67e56160a6d71fffa9c4c2a8f8e7aa9a56aa5a59))

## [0.17.0](https://github.com/ptah-sh/ptah-server/compare/v0.16.0...v0.17.0) (2024-08-16)


### Features

* [#124](https://github.com/ptah-sh/ptah-server/issues/124) add sentry ingegration ([716d2b3](https://github.com/ptah-sh/ptah-server/commit/716d2b314cf1be381746883cc5cd23ee5bae94fe))


### Miscellaneous Chores

* add ptah.sh logo to readme ([3186bf9](https://github.com/ptah-sh/ptah-server/commit/3186bf9f45c14e9e11876137452e3ab27b613ad7))
* fix open source to fair source ([bd8e23f](https://github.com/ptah-sh/ptah-server/commit/bd8e23f3c0ef25427415e484f957d3bcaf0dbda3))
* fix pricing to reflect the latest changes ([bb5869b](https://github.com/ptah-sh/ptah-server/commit/bb5869b7504ddb5beb02f0b8ffbb68bc7674c65b))

## [0.16.0](https://github.com/ptah-sh/ptah-server/compare/v0.15.5...v0.16.0) (2024-08-11)


### Features

* [#114](https://github.com/ptah-sh/ptah-server/issues/114) add swarm count quotas ([859179b](https://github.com/ptah-sh/ptah-server/commit/859179b5e5c6ab25f05dbb5cd5b9816a192ebd91))

## [0.15.5](https://github.com/ptah-sh/ptah-server/compare/v0.15.4...v0.15.5) (2024-08-11)


### Bug Fixes

* [#116](https://github.com/ptah-sh/ptah-server/issues/116) inertia integration ([7b42be7](https://github.com/ptah-sh/ptah-server/commit/7b42be793885461141c9173b25ff89558b993449))

## [0.15.4](https://github.com/ptah-sh/ptah-server/compare/v0.15.3...v0.15.4) (2024-08-11)


### Bug Fixes

* [#116](https://github.com/ptah-sh/ptah-server/issues/116) remove services (processes) when a service has been deleted from ui ([07da9de](https://github.com/ptah-sh/ptah-server/commit/07da9de0d72111bb0a6f78be49dbae64c8d71b05))

## [0.15.3](https://github.com/ptah-sh/ptah-server/compare/v0.15.2...v0.15.3) (2024-08-09)


### Bug Fixes

* [#112](https://github.com/ptah-sh/ptah-server/issues/112) run scheduled commands on server but not on other cli commands ([f716062](https://github.com/ptah-sh/ptah-server/commit/f7160620262857be7b212614d4d28ef85a543fbd))

## [0.15.2](https://github.com/ptah-sh/ptah-server/compare/v0.15.1...v0.15.2) (2024-08-09)


### Bug Fixes

* [#108](https://github.com/ptah-sh/ptah-server/issues/108) workers secrets config creation ([47e80f2](https://github.com/ptah-sh/ptah-server/commit/47e80f266a3cb9a1b0d63958e69a164559ee86ba))

## [0.15.1](https://github.com/ptah-sh/ptah-server/compare/v0.15.0...v0.15.1) (2024-08-09)


### Bug Fixes

* [#107](https://github.com/ptah-sh/ptah-server/issues/107) set default join tokens ([2e1790f](https://github.com/ptah-sh/ptah-server/commit/2e1790f10540e44f7e01cbfbed9f9327a610b09f))
* [#108](https://github.com/ptah-sh/ptah-server/issues/108) create secrets file each deployment ([9e1cdf1](https://github.com/ptah-sh/ptah-server/commit/9e1cdf14063b83d8b7552f65cb5ba5fad43a3939))

## [0.15.0](https://github.com/ptah-sh/ptah-server/compare/v0.14.0...v0.15.0) (2024-08-08)


### Features

* [#68](https://github.com/ptah-sh/ptah-server/issues/68) replace jetstream dashboard with saas-related microdashboard ([bc46fef](https://github.com/ptah-sh/ptah-server/commit/bc46fef19a34afff16485e39b7b5b2e05d87f21f))

## [0.14.0](https://github.com/ptah-sh/ptah-server/compare/v0.13.5...v0.14.0) (2024-08-08)


### Features

* [#104](https://github.com/ptah-sh/ptah-server/issues/104) adjust billing logic to re-imagined pricing ([2a37e3c](https://github.com/ptah-sh/ptah-server/commit/2a37e3ca3cca6e2a58b9c0672588de71dde923b0))

## [0.13.5](https://github.com/ptah-sh/ptah-server/compare/v0.13.4...v0.13.5) (2024-08-05)


### Bug Fixes

* [#94](https://github.com/ptah-sh/ptah-server/issues/94) require billing name and email on team creation ([a57f2b1](https://github.com/ptah-sh/ptah-server/commit/a57f2b1345e8783edc3b90d59aef0375a70db3e8))
* [#96](https://github.com/ptah-sh/ptah-server/issues/96) cancel subscription on team deletion ([543b69b](https://github.com/ptah-sh/ptah-server/commit/543b69b5dd3f02e4d0e5710afad5b1c4eade6690))
* add resend package ([d666c06](https://github.com/ptah-sh/ptah-server/commit/d666c06d6a568d0c8f445f55529e35e69b728bf3))

## [0.13.4](https://github.com/ptah-sh/ptah-server/compare/v0.13.3...v0.13.4) (2024-08-04)


### Bug Fixes

* [#78](https://github.com/ptah-sh/ptah-server/issues/78) fix subscription middleware logic ([53645d9](https://github.com/ptah-sh/ptah-server/commit/53645d9de33c0450d1bf893da2781fdd92b2c764))

## [0.13.3](https://github.com/ptah-sh/ptah-server/compare/v0.13.2...v0.13.3) (2024-08-04)


### Bug Fixes

* [#78](https://github.com/ptah-sh/ptah-server/issues/78) fix trial management ([5c6cba1](https://github.com/ptah-sh/ptah-server/commit/5c6cba19ae5aaefb8f397a89f640338f91554cf8))

## [0.13.2](https://github.com/ptah-sh/ptah-server/compare/v0.13.1...v0.13.2) (2024-08-02)


### Miscellaneous Chores

* [#85](https://github.com/ptah-sh/ptah-server/issues/85) add legal links ([e7290e9](https://github.com/ptah-sh/ptah-server/commit/e7290e9f8c16197c587d7bef50d47b71ff082c8e))

## [0.13.1](https://github.com/ptah-sh/ptah-server/compare/v0.13.0...v0.13.1) (2024-08-02)


### Miscellaneous Chores

* [#4](https://github.com/ptah-sh/ptah-server/issues/4) add ci step ([368f484](https://github.com/ptah-sh/ptah-server/commit/368f48467080847e9040150885dd8d62e218369d))

## [0.13.0](https://github.com/ptah-sh/ptah-server/compare/v0.12.0...v0.13.0) (2024-07-30)


### Features

* [#78](https://github.com/ptah-sh/ptah-server/issues/78) add bcmath ([0884fc9](https://github.com/ptah-sh/ptah-server/commit/0884fc9c2ce2e91cd1f3fe621500a9a63ae42990))


### Bug Fixes

* [#78](https://github.com/ptah-sh/ptah-server/issues/78) dont ask db for jobs if running in console ([de00336](https://github.com/ptah-sh/ptah-server/commit/de00336e12ea8329b0bd6f061ca3f083833b11e4))

## [0.12.0](https://github.com/ptah-sh/ptah-server/compare/v0.11.3...v0.12.0) (2024-07-30)


### Features

* [#35](https://github.com/ptah-sh/ptah-server/issues/35) add 404 handler to caddy ([ee773d9](https://github.com/ptah-sh/ptah-server/commit/ee773d91ad468ce743c035305312cdb1c3dc3946))
* [#63](https://github.com/ptah-sh/ptah-server/issues/63) allow to manage storages for large files ([487ace4](https://github.com/ptah-sh/ptah-server/commit/487ace41a4e03ff4d28d7b73795b5af47fa253f4))
* [#64](https://github.com/ptah-sh/ptah-server/issues/64) allow to run arbitrary backup commands for processes ([698905f](https://github.com/ptah-sh/ptah-server/commit/698905f3076e6d5f39fcc818f68172f1ccd119e3))
* [#65](https://github.com/ptah-sh/ptah-server/issues/65) allow to backup volumes ([3aba5f1](https://github.com/ptah-sh/ptah-server/commit/3aba5f12fca034be82425124bcf0c01591ac91c1))
* [#75](https://github.com/ptah-sh/ptah-server/issues/75) prevent full service shutdown during update ([16fdf9b](https://github.com/ptah-sh/ptah-server/commit/16fdf9b8e03fb46d45f4ed840bc797c9410822e1))
* [#78](https://github.com/ptah-sh/ptah-server/issues/78) add subscriptions with Paddle ([ad0586b](https://github.com/ptah-sh/ptah-server/commit/ad0586b85072d540e58433919f2224b7b83c9310))
* [#79](https://github.com/ptah-sh/ptah-server/issues/79) enable e-mail verification ([d836bb1](https://github.com/ptah-sh/ptah-server/commit/d836bb1dc2e891af8c7964395d51567ef5e711b0))
* [#81](https://github.com/ptah-sh/ptah-server/issues/81) send e-mail notifications regarding ending trial ([00ec406](https://github.com/ptah-sh/ptah-server/commit/00ec40613cf2dad5bf001f14c517f7cfea04bc33))
* [#82](https://github.com/ptah-sh/ptah-server/issues/82) restrict access to the service when a user doesn't have an active subscription ([012785c](https://github.com/ptah-sh/ptah-server/commit/012785cb8997b9a1bf20559d5b669a1c579c9580))
* [#83](https://github.com/ptah-sh/ptah-server/issues/83) allow a node to join an existing cluster ([373e15d](https://github.com/ptah-sh/ptah-server/commit/373e15d82ffab531e5f3fe943fc16a645f4d6931))


### Bug Fixes

* [#74](https://github.com/ptah-sh/ptah-server/issues/74) de-duplicate updated env vars ([22182f8](https://github.com/ptah-sh/ptah-server/commit/22182f8f46d021a4999618d49a34352da9c4ca83))
* [#77](https://github.com/ptah-sh/ptah-server/issues/77) broken docker registry link in the service if the registry is renamed ([ce811ef](https://github.com/ptah-sh/ptah-server/commit/ce811efd4d352743ded723db6b9a0e1c6cd02eb1))

## [0.11.3](https://github.com/ptah-sh/ptah-server/compare/v0.11.2...v0.11.3) (2024-07-11)


### Bug Fixes

* [#72](https://github.com/ptah-sh/ptah-server/issues/72) assign correct team when the service is accessed via api ([0e7cfe4](https://github.com/ptah-sh/ptah-server/commit/0e7cfe4de6d2e2ca2f825e6d254fb7af6a85894f))

## [0.11.2](https://github.com/ptah-sh/ptah-server/compare/v0.11.1...v0.11.2) (2024-07-11)


### Bug Fixes

* [#28](https://github.com/ptah-sh/ptah-server/issues/28) re-order rules ([aa57f3b](https://github.com/ptah-sh/ptah-server/commit/aa57f3b60b1216c537a52ddcd415677794a41a2a))

## [0.11.1](https://github.com/ptah-sh/ptah-server/compare/v0.11.0...v0.11.1) (2024-07-11)


### Bug Fixes

* [#27](https://github.com/ptah-sh/ptah-server/issues/27) access all available teams' resources via api ([9dc3616](https://github.com/ptah-sh/ptah-server/commit/9dc3616b876f364ebc69c5c6ee49d258b4467da0))

## [0.11.0](https://github.com/ptah-sh/ptah-server/compare/v0.10.5...v0.11.0) (2024-07-10)


### Features

* [#28](https://github.com/ptah-sh/ptah-server/issues/28) allow to add redirect rules ([212b6e0](https://github.com/ptah-sh/ptah-server/commit/212b6e0d0f100838b046715b8cc6dcd70841c474))


### Bug Fixes

* emulate prepares for postgres ([bad4e73](https://github.com/ptah-sh/ptah-server/commit/bad4e73a5e1283f7526fe4fc91fa63d31f33a11a))

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
