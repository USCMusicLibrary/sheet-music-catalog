#/bin/bash
python3 ../authoritycheck/authoritycheck.py check_all names &> output.txt
python3 ../authoritycheck/authoritycheck.py check_all subjects &> output2.txt
echo "DONE!"