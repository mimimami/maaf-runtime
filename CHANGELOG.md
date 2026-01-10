# Changelog

## [1.0.0] - 2024-01-XX

### Added

- ✅ **Worker-Based Execution**
  - `WorkerInterface` - Worker interface
  - `WorkerManager` - Worker kezelő Octane szerű futtatáshoz
  - `HttpWorker` - HTTP kérések kezelésére szolgáló worker
  - `WorkerStatus` - Worker státusz enum
  - Worker pool kezelés
  - Max requests per worker

- ✅ **Cached Bootstrap**
  - `BootstrapCache` - Bootstrap cache-elés
  - Cache validáció
  - Cache törlés
  - Teljesítmény javítás

- ✅ **Module-Level Preload**
  - `PreloadManager` - Modul szintű preload kezelés
  - OPcache preload script generálás
  - Modul fájlok automatikus preload-ja
  - Preload script kezelés

- ✅ **Runtime Environment**
  - `Runtime` - Fő runtime osztály
  - Worker alapú futtatás
  - Bootstrap cache integráció
  - Preload integráció

- ✅ **CLI Commands**
  - `RuntimeStartCommand` - Runtime indítása
  - `RuntimeStopCommand` - Runtime leállítása
  - `RuntimeReloadCommand` - Runtime újratöltése
  - `PreloadGenerateCommand` - Preload script generálása
  - `CacheClearCommand` - Bootstrap cache törlése

### Changed
- N/A (első kiadás)

### Fixed
- N/A (első kiadás)
