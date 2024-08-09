import csv
import random
import string

# Funzione per generare un username casuale
def generate_username(length=12):
    characters = string.ascii_letters + string.digits
    return ''.join(random.choice(characters) for i in range(length))

# Funzione per generare un indirizzo email casuale
def generate_email(username, domain_list=['gnails.from', 'yau.com', 'hotmeil.con', 'autluc.de']):
    domain = random.choice(domain_list)
    return f"{username}@{domain}"

# Numero di righe da generare
num_rows = 120

# Creazione del file CSV
with open('output.csv', 'w', newline='') as csvfile:
    writer = csv.writer(csvfile)
    writer.writerow(['username', 'email'])

    for _ in range(num_rows):
        username = generate_username()
        email = generate_email(username)
        writer.writerow([username, email])

print(f"File CSV generato: output.csv")
