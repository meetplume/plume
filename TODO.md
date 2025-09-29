
- Now??
	- Repo, which we clone and put to run, as regular Laravel App


- First goal: Have `meetplume.com` website with an empty landing page, with a logo, and a link to `/docs`
	- This enables:
		- Documentation for the Users
		- Understanding of the project
	- What we have in this point?
		- Installed Plume, normal
		- Make Landing page (override)
		- Docs
			- Install and start with https://github.com/GuavaCZ/filament-knowledge-base , while we implement, we might evolve to a own solution
			- Driver: File/Database
			- Format: MD/RichTextJson
			- Bring stuff from Nexus
			- Start a Collection, Version
				- Ex1: 
					- Collection: "Plume-docs"
					- Version: "v0.x"
					- Language: How?
					- Driver: File
					- Format: MD
					- Path: `/docs/plume-docs/v0.x`  
						- `index.md`
	- Routes:
		- `/docs/{lang}/{collection}/{version}/about  (about.md)
	- Render
		- Content
		- Table of Contents


