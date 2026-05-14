function Get-Tree {
    param($Path, $Indent = "")
    $items = Get-ChildItem -Path $Path
    foreach ($item in $items) {
        if ($item.Name -match "vendor|node_modules|\.git|\.gemini|\.agents") {
            Write-Output "$Indent|- $($item.Name)/ [Ignored Content]"
        } elseif ($item.PSIsContainer) {
            Write-Output "$Indent|- $($item.Name)/"
            if ($item.Name -match "storage") {
                # Only show top level of storage to avoid view/cache spam
                Write-Output "$Indent  |- app/"
                Write-Output "$Indent  |- framework/ [Ignored Content]"
                Write-Output "$Indent  |- logs/"
            } else {
                Get-Tree -Path $item.FullName -Indent "$Indent  "
            }
        } else {
            Write-Output "$Indent|- $($item.Name)"
        }
    }
}
Get-Tree -Path "."
