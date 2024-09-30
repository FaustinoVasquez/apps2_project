#!/bin/bash
tsql -S 192.168.0.236 -U tempuser -P pLa13t1B <<EOS
EXECUTE [Inventory].[dbo].[sp_CreateInventoryAnalyticsRecursive] '$1','$2','$3'
GO
exit
EOS
