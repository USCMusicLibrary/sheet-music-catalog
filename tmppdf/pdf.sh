#/bin/bash
convert  tmppdf/img/* tmppdf/tmppdf.pdf
rm -rf tmppdf/img/*
echo "DONE!"