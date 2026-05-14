---
trigger: manual
description: Updates the PROJECT_STRUCTURE.md file and synchronizes the new tree with the Agent's Knowledge using a defensive PowerShell check.
---

# Update Project Tree Workflow

You are now executing an automated workflow to update the project structure documentation. Please execute the following steps sequentially and without asking for my intervention:

1. Execute the following command in the terminal to safely generate the project tree:
   `if (Test-Path ".\scratch\generate_tree.ps1") { .\scratch\generate_tree.ps1 > PROJECT_STRUCTURE.md } else { Write-Error "Script not found at .\scratch\generate_tree.ps1" }`

2. Wait for the terminal command to complete successfully.

3. Read the entire content of the newly generated `PROJECT_STRUCTURE.md` file.

4. Update the Knowledge Item related to the "Project Structure" (e.g., `project_structure.md`) with this new content, ensuring it is saved as your permanent reference for the project architecture.

5. Notify me with a brief message once the update is successfully completed.