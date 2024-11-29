import csv
import random
import string
import argparse

# Funzione per generare un username casuale
def generate_username(length=12):
    characters = string.ascii_letters + string.digits
    return ''.join(random.choice(characters) for i in range(length))

# Funzione per generare un indirizzo email casuale
def generate_email(username, domain_list=['gnails.from', 'yau.com', 'hotmeil.con', 'autluc.de']):
    domain = random.choice(domain_list)
    return f"{username}@{domain}"

# Funzione per generare un nome casuale
def generate_first_name():
    first_names = ['Mario', 'Luigi', 'Giulia', 'Marco', 'Anna', 'Sofia', 'Giorgio', 'Elena', 'Luca', 'Francesca']
    return random.choice(first_names)

# Funzione per generare un cognome casuale
def generate_last_name():
    last_names = ['Rossi', 'Bianchi', 'Verdi', 'Neri', 'Gallo', 'Ferrari', 'Conti', 'Esposito', 'Ricci', 'Marini']
    return random.choice(last_names)

# Funzione per generare il file CSV
def generate_csv(include_names, num_rows=120, output_file='output.csv'):
    with open(output_file, 'w', newline='') as csvfile:
        writer = csv.writer(csvfile)

        # Intestazione del CSV
        if include_names:
            writer.writerow(['username', 'email', 'first_name', 'last_name'])
        else:
            writer.writerow(['username', 'email'])

        # Scrittura dei dati
        for _ in range(num_rows):
            username = generate_username()
            email = generate_email(username)
            
            if include_names:
                first_name = generate_first_name()
                last_name = generate_last_name()
                writer.writerow([username, email, first_name, last_name])
            else:
                writer.writerow([username, email])

    print(f"CSV file generated: {output_file}")

# Parser degli argomenti da riga di comando
if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='Generate a CSV file with random user data.')
    parser.add_argument('--include-names', action='store_true', help='Include first and last names in the generated CSV')
    parser.add_argument('--num-rows', type=int, default=120, help='Number of rows to generate (default: 120)')
    parser.add_argument('--output-file', type=str, default='output.csv', help='Output CSV file name (default: output.csv)')

    args = parser.parse_args()

    # Generazione del file CSV
    generate_csv(args.include_names, args.num_rows, args.output_file)
