import ViewWithHeader from "../../components/headers/ViewWithHeader";

function PrivacyPolicy() {
  return (
    <ViewWithHeader text="Politique de confidentialité">
      <div className="px-6 pt-6 pb-20 text-sm bg-light-grey font-default">
        <p className="mb-6">
          VanScape respecte votre vie privée. Cette page vous informe de la
          manière dont nous collectons, utilisons et protégeons vos données
          personnelles.
        </p>

        <section className="mb-6">
          <h2 className="text-lg font-medium mb-2">Données collectées</h2>
          <p>
            Nous collectons uniquement les données nécessaires à l’utilisation
            du service : adresse email, géolocalisation des spots, pseudonyme.
          </p>
        </section>

        <section className="mb-6">
          <h2 className="text-lg font-medium mb-2">Utilisation</h2>
          <p>
            Les données sont utilisées uniquement pour fournir les services de
            l’application : connexion, partage de spots, notifications entre
            utilisateurs.
          </p>
        </section>

        <section className="mb-6">
          <h2 className="text-lg font-medium mb-2">Conservation</h2>
          <p>
            Les données sont conservées pendant 3 ans après la dernière
            activité, sauf demande explicite de suppression de votre part.
          </p>
        </section>

        <section className="mb-6">
          <h2 className="text-lg font-medium mb-2">Hébergement</h2>
          <p>
            Les données sont hébergées sur un serveur personnel situé en France,
            administré par la responsable de la publication. L'accès à ce
            serveur est sécurisé, et les données ne sont accessibles qu'à des
            fins techniques et de maintenance.
          </p>
          <p>
            Cet hébergement respecte les bonnes pratiques de sécurité (accès
            restreint, sauvegardes, HTTPS) et vise à garantir la confidentialité
            et l’intégrité des données, en conformité avec le Règlement Général
            sur la Protection des Données (RGPD).
          </p>
        </section>

        <section className="mb-6">
          <h2 className="text-lg font-medium mb-2">Droits des utilisateurs</h2>
          <p>
            Vous pouvez exercer vos droits d’accès, de rectification ou de
            suppression de vos données personnelles à tout moment en nous
            contactant à l’adresse :{" "}
            <a
              href="mailto:vanscape.contact@gmail.com"
              className="underline text-dark-green hover:text-dark-green-hover"
            >
              vanscape.contact@gmail.com
            </a>
          </p>
        </section>
      </div>
    </ViewWithHeader>
  );
}

export default PrivacyPolicy;
