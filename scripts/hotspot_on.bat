adb devices
sleep 1
adb shell am start -n com.android.settings/.TetherSettings
sleep 1
adb shell input keyevent 66
sleep 1
adb shell input keyevent 66
sleep 1
adb shell input keyevent 4
sleep 1
adb shell input keyevent 4
sleep 1
exit