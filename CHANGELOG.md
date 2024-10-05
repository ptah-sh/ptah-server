# Changelog

## [0.32.0](https://github.com/ptah-sh/ptah-server/compare/v0.31.1...v0.32.0) (2024-10-05)


### Features

* [#206](https://github.com/ptah-sh/ptah-server/issues/206) display usage graphs for nodes ([4035ef8](https://github.com/ptah-sh/ptah-server/commit/4035ef849738a6fb5787012bea6ecb5da74f285b))
* [#207](https://github.com/ptah-sh/ptah-server/issues/207) allow to collect metrics from non-swarm nodes ([cb4f077](https://github.com/ptah-sh/ptah-server/commit/cb4f077d470994ff21af263b299986c183e00b70))
* [#207](https://github.com/ptah-sh/ptah-server/issues/207) metrics ingestion endpoint v0.1 ([d94f130](https://github.com/ptah-sh/ptah-server/commit/d94f1302cce15c5abca1a205d5788c8ad478f2f4))
* [#207](https://github.com/ptah-sh/ptah-server/issues/207) metrics ingestion endpoint v0.2 ([d9b1a5d](https://github.com/ptah-sh/ptah-server/commit/d9b1a5d833f5fd4776480dfa0d29f04f743185d9))
* [#209](https://github.com/ptah-sh/ptah-server/issues/209) install victoriametrics as a part of self-hosted workflow ([b0280d2](https://github.com/ptah-sh/ptah-server/commit/b0280d200b8504620ea553ea6f11fd772f246e31))


### Miscellaneous Chores

* update installation data ([ee2f3ef](https://github.com/ptah-sh/ptah-server/commit/ee2f3efcb36b336ea4393317b0bc6ac332c476b9))

## [0.31.1](https://github.com/ptah-sh/ptah-server/compare/v0.31.0...v0.31.1) (2024-09-26)


### Bug Fixes

* [#205](https://github.com/ptah-sh/ptah-server/issues/205) preserve secret vars value ([4cc6dc1](https://github.com/ptah-sh/ptah-server/commit/4cc6dc11245927e7a29a24c0187fc08a33a899a9))
* minor inconsistencies ([e1c238b](https://github.com/ptah-sh/ptah-server/commit/e1c238be8b5f233e294530c289dbd0b947f826ec))

## [0.31.0](https://github.com/ptah-sh/ptah-server/compare/v0.30.0...v0.31.0) (2024-09-24)


### Features

* [#199](https://github.com/ptah-sh/ptah-server/issues/199) support docker registry username encryption ([4241af6](https://github.com/ptah-sh/ptah-server/commit/4241af61aa0c4bb2c90781cd188f2946793d55b0))
* [#200](https://github.com/ptah-sh/ptah-server/issues/200) reset deployments quota each day ([a6490df](https://github.com/ptah-sh/ptah-server/commit/a6490df6653454d3d38cac3eccc2156aad367cf6))


### Miscellaneous Chores

* update installation data ([21371d9](https://github.com/ptah-sh/ptah-server/commit/21371d9c1ac296e50e9e19de84e21b6e76256a2f))

## [0.30.0](https://github.com/ptah-sh/ptah-server/compare/v0.29.0...v0.30.0) (2024-09-23)


### Features

* [#117](https://github.com/ptah-sh/ptah-server/issues/117) rebuild caddy config once the service has been deleted ([bfe9996](https://github.com/ptah-sh/ptah-server/commit/bfe999664c4a8a945b786a1dfe6b7f6e78ac1256))
* [#135](https://github.com/ptah-sh/ptah-server/issues/135) add link to expression language docs ([7f2a705](https://github.com/ptah-sh/ptah-server/commit/7f2a705e6710abbbc910df0523627be8d854c8a8))
* [#135](https://github.com/ptah-sh/ptah-server/issues/135) add support for generated secrets in templates ([aa23493](https://github.com/ptah-sh/ptah-server/commit/aa23493328426bba84a8d4e0b7eccfc6e4393cf9))
* [#135](https://github.com/ptah-sh/ptah-server/issues/135) add support for generated values for custom env and secret vars ([f65af67](https://github.com/ptah-sh/ptah-server/commit/f65af67e023c4422e7cc3b780d4d47fa3a24704d))
* [#137](https://github.com/ptah-sh/ptah-server/issues/137), [#161](https://github.com/ptah-sh/ptah-server/issues/161) ask mid and end -trial feedback ([c2f0482](https://github.com/ptah-sh/ptah-server/commit/c2f04824c3e8cd265009bf9d9267f1f70c5b12a2))
* [#17](https://github.com/ptah-sh/ptah-server/issues/17) support secrets encryption ([2b44d94](https://github.com/ptah-sh/ptah-server/commit/2b44d94e3f0600f49bf09abcd3b26519f0146894))
* [#189](https://github.com/ptah-sh/ptah-server/issues/189) pick a single node as a placement server for volume'd templates ([efc958a](https://github.com/ptah-sh/ptah-server/commit/efc958ad9a6c0857019fe25aaa01797d17bb1baf))
* [#192](https://github.com/ptah-sh/ptah-server/issues/192) correct wording in a trial ends notification ([59e4def](https://github.com/ptah-sh/ptah-server/commit/59e4def2f19de00ae1beb40387f64d4213e19537))
* [#195](https://github.com/ptah-sh/ptah-server/issues/195) improve backups folder layout ([735797f](https://github.com/ptah-sh/ptah-server/commit/735797fcbdd5c8663e0aa3de0dda1148c8a42855))
* [#196](https://github.com/ptah-sh/ptah-server/issues/196) replace create/update service with a launch service task ([a4313b0](https://github.com/ptah-sh/ptah-server/commit/a4313b026a7889e2a164424c54a697793e1b1ca1))
* [#197](https://github.com/ptah-sh/ptah-server/issues/197) switch hobby plan to the yearly payment ([93aa08d](https://github.com/ptah-sh/ptah-server/commit/93aa08d2b67a4f9b8de51e201e38710aed1b7b8b))
* [#51](https://github.com/ptah-sh/ptah-server/issues/51) allow to specify healthchecks ([0a1fb42](https://github.com/ptah-sh/ptah-server/commit/0a1fb42f15b0e0dc57e14c30994d199877684831))
* [#98](https://github.com/ptah-sh/ptah-server/issues/98) bootstrap admin suite ([71d5049](https://github.com/ptah-sh/ptah-server/commit/71d504934e9b8993c63569d81ae539afda1cf15a))
* pricing test ([82c0687](https://github.com/ptah-sh/ptah-server/commit/82c068726789573d12640fde3f028e5e43071a97))


### Bug Fixes

* [#179](https://github.com/ptah-sh/ptah-server/issues/179) protect caddy admin port from an outside actors ([91df9f8](https://github.com/ptah-sh/ptah-server/commit/91df9f8b6929c1e7e3e44cef18e703c6c17b2cf6))
* [#192](https://github.com/ptah-sh/ptah-server/issues/192) fix trial end date calculation ([b2cef50](https://github.com/ptah-sh/ptah-server/commit/b2cef50d068451f48c31f09639d5314d7fa1ab7b))
* [#39](https://github.com/ptah-sh/ptah-server/issues/39) display correct latest task ([1e03b6c](https://github.com/ptah-sh/ptah-server/commit/1e03b6c89fe39900ef3b1a0e3e769b5f8ad93bc9))
* [#39](https://github.com/ptah-sh/ptah-server/issues/39) display correct latest task 2 ([d9e1aab](https://github.com/ptah-sh/ptah-server/commit/d9e1aab26422b65f262ecf9424ea0fca55362404))
* [#51](https://github.com/ptah-sh/ptah-server/issues/51) self-hosted configurator ([ca15de3](https://github.com/ptah-sh/ptah-server/commit/ca15de3bf86e6b07b6dbf87be1e859b9ad694ab6))


### Miscellaneous Chores

* update installation data ([c829d16](https://github.com/ptah-sh/ptah-server/commit/c829d162f38225c838a26f362bd299abdcc051ca))
* update installation data ([2bdf780](https://github.com/ptah-sh/ptah-server/commit/2bdf780fb066141cfe5f4d24771014dbd18cb0e6))
* update installation data ([ce4d5b2](https://github.com/ptah-sh/ptah-server/commit/ce4d5b2711c1e8f1a6a5657049397a9385eb4653))
* update installation data ([dfdcb34](https://github.com/ptah-sh/ptah-server/commit/dfdcb349e3195f7c674045748cc5a25697a77d5f))
* update installation data ([a461ecc](https://github.com/ptah-sh/ptah-server/commit/a461eccbb1004a8474de55529a46c95392e65abb))
* update installation data ([b051b65](https://github.com/ptah-sh/ptah-server/commit/b051b658b7edbf9d02d50f7af1136bc92e72413a))

## [0.29.0](https://github.com/ptah-sh/ptah-server/compare/v0.28.0...v0.29.0) (2024-09-14)


### Features

* [#192](https://github.com/ptah-sh/ptah-server/issues/192) move paywall to the end of the trial period ([6684c32](https://github.com/ptah-sh/ptah-server/commit/6684c32e045207a10d0dea9732b962a1e43995ed))

## [0.28.0](https://github.com/ptah-sh/ptah-server/compare/v0.27.6...v0.28.0) (2024-09-13)


### Features

* [#88](https://github.com/ptah-sh/ptah-server/issues/88) add the quotas page, ensure quotas for deployments ([dce31ac](https://github.com/ptah-sh/ptah-server/commit/dce31ac07c64aee39355f01da2abd06af1cf1bc3))


### Bug Fixes

* [#191](https://github.com/ptah-sh/ptah-server/issues/191) correct subsequent deployments ([588417b](https://github.com/ptah-sh/ptah-server/commit/588417bb0e3348e25285ff2fbea2bd305d901c0c))
* license name and rewrite rules form location ([1e75e6c](https://github.com/ptah-sh/ptah-server/commit/1e75e6c03b7042fda2e017fbd212b7c708ad43c2))

## [0.27.6](https://github.com/ptah-sh/ptah-server/compare/v0.27.5...v0.27.6) (2024-09-08)


### Bug Fixes

* [#69](https://github.com/ptah-sh/ptah-server/issues/69) use docker/metadata-action to create labels 7 ([869f4bc](https://github.com/ptah-sh/ptah-server/commit/869f4bc7ee0467334a07eac40521fd6f9d2889a5))

## [0.27.5](https://github.com/ptah-sh/ptah-server/compare/v0.27.4...v0.27.5) (2024-09-08)


### Bug Fixes

* [#69](https://github.com/ptah-sh/ptah-server/issues/69) use docker/metadata-action to create labels 6 ([02d530a](https://github.com/ptah-sh/ptah-server/commit/02d530a4c78ee5f525f773c0ea91e1e34c3e1e82))

## [0.27.4](https://github.com/ptah-sh/ptah-server/compare/v0.27.3...v0.27.4) (2024-09-08)


### Bug Fixes

* [#69](https://github.com/ptah-sh/ptah-server/issues/69) use docker/metadata-action to create labels 5 ([b8513fd](https://github.com/ptah-sh/ptah-server/commit/b8513fd3f731cf5aa1c0c172eddcfd9436c3ef89))

## [0.27.3](https://github.com/ptah-sh/ptah-server/compare/v0.27.2...v0.27.3) (2024-09-08)


### Bug Fixes

* [#69](https://github.com/ptah-sh/ptah-server/issues/69) use docker/metadata-action to create labels 4 ([e7eba13](https://github.com/ptah-sh/ptah-server/commit/e7eba139ea002f8df77e24e06b565fdb792144df))

## [0.27.2](https://github.com/ptah-sh/ptah-server/compare/v0.27.1...v0.27.2) (2024-09-08)


### Bug Fixes

* [#69](https://github.com/ptah-sh/ptah-server/issues/69) use docker/metadata-action to create labels 3 ([706a5ea](https://github.com/ptah-sh/ptah-server/commit/706a5ea96d3e32c004d13029b74739ba8c2cb2dd))

## [0.27.1](https://github.com/ptah-sh/ptah-server/compare/v0.27.0...v0.27.1) (2024-09-08)


### Bug Fixes

* [#69](https://github.com/ptah-sh/ptah-server/issues/69) use docker/metadata-action to create labels 2 ([0e03730](https://github.com/ptah-sh/ptah-server/commit/0e03730eeafc20674a28f1b344889dbf37ad7e2b))

## [0.27.0](https://github.com/ptah-sh/ptah-server/compare/v0.26.2...v0.27.0) (2024-09-08)


### Features

* [#41](https://github.com/ptah-sh/ptah-server/issues/41) move placementNodeId to processes and require it's presence ([5897cc9](https://github.com/ptah-sh/ptah-server/commit/5897cc97a576ff67d82cc81db9a98b2b10147d71))
* [#69](https://github.com/ptah-sh/ptah-server/issues/69) use docker/metadata-action to create labels ([977f154](https://github.com/ptah-sh/ptah-server/commit/977f154780afa9293ea74074776c6ca967c2f9ea))


### Bug Fixes

* [#41](https://github.com/ptah-sh/ptah-server/issues/41) self-host configuration ([2bc7a66](https://github.com/ptah-sh/ptah-server/commit/2bc7a665e25030d1e780b730900f58af8de0045b))


### Miscellaneous Chores

* update installation data ([db6d4d7](https://github.com/ptah-sh/ptah-server/commit/db6d4d7c9ed8a6a388dcd0ebe69e2d2da3e4cf67))

## [0.26.2](https://github.com/ptah-sh/ptah-server/compare/v0.26.1...v0.26.2) (2024-09-08)


### Bug Fixes

* [#174](https://github.com/ptah-sh/ptah-server/issues/174) use root to exec the backup command ([9b40161](https://github.com/ptah-sh/ptah-server/commit/9b40161ff8054f609400c855236657eddc6f805e))
* include tasks and db files ([d5d93c4](https://github.com/ptah-sh/ptah-server/commit/d5d93c4f58efb069bb409753850bb8a43e57884b))


### Miscellaneous Chores

* update installation data ([061ca3a](https://github.com/ptah-sh/ptah-server/commit/061ca3ad06db9bbd553aa8f6f0b655dbe3db88b6))

## [0.26.1](https://github.com/ptah-sh/ptah-server/compare/v0.26.0...v0.26.1) (2024-09-06)


### Bug Fixes

* [#174](https://github.com/ptah-sh/ptah-server/issues/174) ignore deleted services ([fc56048](https://github.com/ptah-sh/ptah-server/commit/fc56048c5e7101a4fa80f0aa478470bdfb05a5ba))

## [0.26.0](https://github.com/ptah-sh/ptah-server/compare/v0.25.0...v0.26.0) (2024-09-06)


### Features

* [#172](https://github.com/ptah-sh/ptah-server/issues/172) use stable agent token for the generated data ([a9f5c2b](https://github.com/ptah-sh/ptah-server/commit/a9f5c2b13c0bc41d4764b2a355fd6debc0961858))


### Bug Fixes

* avoid failure on an empty commits ([46ae558](https://github.com/ptah-sh/ptah-server/commit/46ae558511cc304c4a8505bcb5f4e4a0b137739f))
* fallback to empty rewrite rules ([39a3b57](https://github.com/ptah-sh/ptah-server/commit/39a3b572cd443f1ff751d70f6da15c166bf3b97f))


### Miscellaneous Chores

* update installation data ([62362f3](https://github.com/ptah-sh/ptah-server/commit/62362f369cf35fa52c2687565d280c9aabb6b5b3))
* update installation data ([e59fc5e](https://github.com/ptah-sh/ptah-server/commit/e59fc5e582bb9de292a6e3a0469c34813922a878))

## [0.25.0](https://github.com/ptah-sh/ptah-server/compare/v0.24.2...v0.25.0) (2024-09-06)


### Features

* [#172](https://github.com/ptah-sh/ptah-server/issues/172) use stable ids pool for the generated data ([c68cc00](https://github.com/ptah-sh/ptah-server/commit/c68cc00099de887f422e0fee931d836794892b24))


### Bug Fixes

* add id to the generated rewrite rules ([fd8f49a](https://github.com/ptah-sh/ptah-server/commit/fd8f49aa7ce10bc89ab6e9fa2d97e6f7f781c822))


### Miscellaneous Chores

* update installation data ([622d656](https://github.com/ptah-sh/ptah-server/commit/622d65636973b7f9212c10966302bb8ee7efe05f))
* update installation data ([87a87c9](https://github.com/ptah-sh/ptah-server/commit/87a87c9c449118a9c2840df934a2bb2012cdb2f5))

## [0.24.2](https://github.com/ptah-sh/ptah-server/compare/v0.24.1...v0.24.2) (2024-09-06)


### Bug Fixes

* rewrite rules default value ([b7e021a](https://github.com/ptah-sh/ptah-server/commit/b7e021acd9c8a5dd85219bf20e6b9f9a5b2ee58b))
* rewrite rules default value - 2 ([2694d21](https://github.com/ptah-sh/ptah-server/commit/2694d21fa168ff0bd685d368443fc5061f6c7f6a))

## [0.24.1](https://github.com/ptah-sh/ptah-server/compare/v0.24.0...v0.24.1) (2024-09-06)


### Bug Fixes

* try disable git hooks ([ca318e2](https://github.com/ptah-sh/ptah-server/commit/ca318e2ffb9c25e96b2dfb5173e8c2f9207bb000))

## [0.24.0](https://github.com/ptah-sh/ptah-server/compare/v0.23.1...v0.24.0) (2024-09-06)


### Features

* [#162](https://github.com/ptah-sh/ptah-server/issues/162) create install-server script as well ([15a0e8f](https://github.com/ptah-sh/ptah-server/commit/15a0e8fbdcdd4c4df4877ecfc4b939a0856bb2f8))
* [#168](https://github.com/ptah-sh/ptah-server/issues/168) support rewrite rules ([d4f0779](https://github.com/ptah-sh/ptah-server/commit/d4f077995457289f924cac623dfe8fb6e11874e4))


### Miscellaneous Chores

* update installation data ([c148295](https://github.com/ptah-sh/ptah-server/commit/c1482959001c7beec8c30fd542e061c41b3d80b2))
* update installation data ([6413ef1](https://github.com/ptah-sh/ptah-server/commit/6413ef13b19754ec95a83117e5d3ea3408e7f66d))

## [0.23.1](https://github.com/ptah-sh/ptah-server/compare/v0.23.0...v0.23.1) (2024-09-03)


### Bug Fixes

* deploy webhook ([50d769f](https://github.com/ptah-sh/ptah-server/commit/50d769f145312412a78f80b31bb17a271bdfbbcc))


### Miscellaneous Chores

* update installation data ([e6449b8](https://github.com/ptah-sh/ptah-server/commit/e6449b8a1567846072093c875ffbbbc25ef14f69))

## [0.23.0](https://github.com/ptah-sh/ptah-server/compare/v0.22.8...v0.23.0) (2024-09-02)


### Features

* [#162](https://github.com/ptah-sh/ptah-server/issues/162) self-hosting scripts ([3521031](https://github.com/ptah-sh/ptah-server/commit/352103164b2a1264e753677fa5a0a7be042be4c0))


### Bug Fixes

* repo link ([fda09e4](https://github.com/ptah-sh/ptah-server/commit/fda09e4e1ef0171feb691765ab506c04f7485164))


### Miscellaneous Chores

* update installation data ([e00bd97](https://github.com/ptah-sh/ptah-server/commit/e00bd976aad5c3b9fb6494d8384816fff49ce862))

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
