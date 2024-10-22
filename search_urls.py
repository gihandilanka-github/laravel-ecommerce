import os
from googlesearch import search
import requests
from bs4 import BeautifulSoup
import json
import joblib
from sklearn.feature_extraction.text import TfidfVectorizer

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# Load the trained classifier and vectorizer
classifier_path = os.path.join(BASE_DIR, 'models', 'classifier.pkl')
vectorizer_path = os.path.join(BASE_DIR, 'models', 'vectorizer.pkl')

classifier = joblib.load(classifier_path)
vectorizer = joblib.load(vectorizer_path)

def find_offers(query):
    search_query = f"{query} Sri Lanka"
    urls = [url for url in search(search_query, stop=50)]  # Retrieve a broader list of URLs
    return urls

def is_sri_lankan_website(url):
    try:
        response = requests.get(url)
        soup = BeautifulSoup(response.content, 'html.parser')
        text = ' '.join([p.text for p in soup.find_all('p')])
        X = vectorizer.transform([text])
        prediction = classifier.predict(X)
        return prediction[0] == 'srilanka'
    except:
        return False

def scrape_offers(urls):
    offers = []
    for url in urls:
        if is_sri_lankan_website(url):
            response = requests.get(url)
            soup = BeautifulSoup(response.content, 'html.parser')
            for offer in soup.select('.offer-item'):
                title = offer.select_one('.offer-title').text if offer.select_one('.offer-title') else 'N/A'
                price = offer.select_one('.offer-price').text if offer.select_one('.offer-price') else 'N/A'
                offers.append({
                    'title': title,
                    'price': price,
                    'url': url,
                })
    return offers

def main(query):
    urls = find_offers(query)
    offers = scrape_offers(urls)
    return json.dumps(offers)

if __name__ == '__main__':
    import sys
    query = sys.argv[1]
    print(main(query))