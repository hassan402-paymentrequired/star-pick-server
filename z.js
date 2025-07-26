import puppeteer from 'puppeteer';
import fs from 'fs';

(async () => {
    const browser = await puppeteer.launch({ headless: true });
    const page = await browser.newPage();

    await page.setExtraHTTPHeaders({
        'Accept': 'application/json',
        'Accept-Language': 'en-US,en;q=0.9',
    });

    await page.setUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/138.0.0.0 Safari/537.36');

    const response = await page.goto('https://www.sofascore.com/api/v1/sport/football/categories', {
        waitUntil: 'networkidle2',
    });

    const content = await response.text();
    fs.writeFileSync('categories.json', content);

    console.log('✅ Data saved to categories.json');
    await browser.close();
})();
