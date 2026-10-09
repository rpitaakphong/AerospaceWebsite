# 2024 capstone ingestion notes

This folder contains the website-ready editorial data and one representative report image for each unique 2024 capstone project supplied for ingestion.

## Inventory

- Six PDF files were supplied.
- Five unique projects were identified.
- `Final Design Report - High.pdf` and `SkyReachHor_Eng_Project_Final_report-655953-17477585387480.pdf` are byte-for-byte identical (SHA-256 `e3232c04d850fc281b6ddb330e97391869a73ab8c6363383578d01c94463ff82`). SkyReach Horizon is therefore stored once, with the second filename retained in `duplicateSources`.

## Editorial conventions

- The collection year is stored as `2024`, following the user-provided cohort/year label. The reports themselves are dated April or May 2025.
- Student ID numbers were intentionally omitted from the website dataset.
- Metrics are labeled as tested, simulated, calculated, estimated or design limits. A number appearing in a final report is not presented as measured performance unless the report describes a corresponding physical test.
- Report wording was condensed for a public academic audience. Technical scope and limitations were retained.
- Advisor names are transcribed as printed in each report. The SSUAS and Mortar Glide Kit reports contain surname spellings that may need confirmation before publication.
- Source PDFs were not copied into this workspace. `projects.json` keeps the original filename and SHA-256 hash so a later publication pipeline can associate each entry with its report download.

## Extracted images

All images are crops of figures embedded in the supplied reports. No generative imagery or stock photography was introduced.

| Project | Asset | Report source |
|---|---|---|
| SSUAS | `assets/ssuas-internal-structure.jpg` | Figure 50, report page 97 / PDF page 100 |
| Mortar Glide Kit | `assets/mortar-glide-kit-isometric.jpg` | Figure 117, report page 140 / PDF page 156 |
| Medical Delivery Drone | `assets/medical-delivery-drone.jpg` | Figure 13-1, report/PDF page 103 |
| SkyReach Horizon | `assets/skyreach-rocket-configuration.jpg` | Figure 167, report/PDF page 174 |
| RV Pathum 01 | `assets/rv-pathum-top-view.jpg` | Figure 30, report/PDF page 70 |

Before public launch, confirm that the program has permission to republish the student reports and their figures, and decide whether the two defense-oriented reports should be publicly downloadable or represented only by an academic project summary.
