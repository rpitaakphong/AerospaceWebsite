# 2023 capstone ingestion notes

This folder contains website-ready editorial data and one representative report image for each unique 2023 capstone project supplied for ingestion.

## Inventory

- Six PDFs were supplied.
- Five unique projects were identified.
- FISH.me was supplied as a full-resolution report and a separately compressed edition. They describe the same project but are not byte-identical and have different pagination. The full-resolution PDF is the primary source because it provides the better image; the compressed filename and hash are retained in `sourceVariants`.

## Editorial conventions

- The collection year is stored as `2023`, following the user-provided cohort label. The reports themselves are dated 30 April 2024.
- Student ID numbers were intentionally omitted.
- Metrics are explicitly labeled as measured, tested, simulated, calculated, estimated or design targets.
- Project limitations were retained. In particular, ALMOND did not complete flight testing, AirMED did not demonstrate autonomous delivery or its required cold-chain performance, and STARS remains primarily a simulation and machine-vision project.
- Report wording was condensed for a public academic audience without treating calculated performance as demonstrated performance.
- Source PDFs were not copied into this workspace. Each entry retains its original filename and SHA-256 hash.

## Extracted images

All images are crops of project figures in the supplied reports. No stock or generative imagery was introduced.

| Project | Asset | Report source |
|---|---|---|
| BWB Commercial Aircraft | `assets/bwb-commercial-aircraft.jpg` | Figure 116, report page 144 / PDF page 169 |
| ALMOND | `assets/almond-prototype.jpg` | Figure 128, report page 174 / PDF page 184 |
| FISH.me | `assets/fishme-uav.jpg` | Figure 7.16, report/PDF page 121 of the full-resolution report |
| AirMED | `assets/airmed-uav.jpg` | Figure 211, report page 298 / PDF page 327 |
| STARS | `assets/stars-mars-helicopter.jpg` | Figure 254, report page 296 / PDF page 314 |

Before publication, confirm permission to reproduce student figures and decide whether the defense-oriented ALMOND report should be downloadable publicly or represented only through a high-level academic summary.
