#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <HardwareSerial.h>

// WiFi credentials
const char* ssid = "realme8i";
const char* pass = "12345678";

const char* serverIP = "172.20.10.2";
const int serverPort = 8000;
const char* apiPath = "/api/readings";
// pH sensor and serial pins
#define PH_SENSOR_PIN 34
#define RXD2 16
#define TXD2 17

// Calibration values
float voltageAtPH7 = 1.72;
float slope = 5.7;

// Timing
unsigned long lastSMSTime = 0;
const unsigned long smsInterval = 60000; // 1 minute between SMS

// LCD & SIM900A
LiquidCrystal_I2C lcd(0x27, 16, 2);
HardwareSerial sim900(2);  // UART2 for SIM900A

void setup() {
  Serial.begin(115200);
  sim900.begin(9600, SERIAL_8N1, RXD2, TXD2);
  lcd.init();
  lcd.backlight();

  lcd.setCursor(0, 0);
  lcd.print("Connecting WiFi");

  WiFi.begin(ssid, pass);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.print(".");
  }

  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("PH LEVEL:");
  Serial.println("WiFi Connected");

  delay(3000); // SIM900A boot delay
}

void loop() {
  int adcValue = analogRead(PH_SENSOR_PIN);
  float voltage = adcValue * 3.3 / 4095.0;
  float pHValue = slope * (voltage - voltageAtPH7) + 7.0;

  // LCD Display
  lcd.setCursor(0, 1);
  lcd.print("pH: ");
  lcd.print(pHValue, 2);
  lcd.print("     ");

  // Serial Monitor
  Serial.print("ADC: ");
  Serial.print(adcValue);
  Serial.print(" | Voltage: ");
  Serial.print(voltage, 3);
  Serial.print(" V | pH: ");
  Serial.println(pHValue, 2);

  // Send to server
  sendToServer(pHValue);

  // Send SMS if out of range
  if ((pHValue < 6.5 || pHValue > 7.5) && (millis() - lastSMSTime > smsInterval)) {
    sendSMS(pHValue);
    lastSMSTime = millis();
  }

  delay(10000); // Wait 10 seconds
}

void sendToServer(float ph) {
   if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;

    String url = String(apiPath);  // Just the endpoint path
    http.begin(serverIP, serverPort, apiPath); // ESP32 HTTPClient supports this overload
    http.addHeader("Content-Type", "application/json");

    String json = "{\"ph\": " + String(ph, 2) + "}";

    int httpResponseCode = http.POST(json);
    Serial.print("HTTP Response code: ");
    Serial.println(httpResponseCode);

    if (httpResponseCode > 0) {
      String response = http.getString();
      Serial.println("Server response: " + response);
    } else {
      Serial.println("Error sending POST request");
    }

    http.end();
  } else {
    Serial.println("WiFi not connected!");
  }
}

void sendSMS(float ph) {
  String message;

  if (ph < 6.5) {
    message = "pH " + String(ph, 2) + " is LOW. Check water.";
  } else if (ph > 7.5) {
    message = "pH " + String(ph, 2) + " is HIGH. Check water.";
  } else {
    return;
  }

  Serial.println("Sending SMS...");
  sim900.println("AT");
  delay(1000);
  sim900.println("AT+CMGF=1");
  delay(1000);
  sim900.println("AT+CMGS=\"+639280735205\""); // Change number as needed
  delay(1000);
  sim900.print(message);
  sim900.write(26); // CTRL+Z to send SMS
  delay(5000);
  Serial.println("SMS Sent: " + message);
}
