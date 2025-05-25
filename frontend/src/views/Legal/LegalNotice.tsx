import ViewWithHeader from "../../components/headers/ViewWithHeader";

function LegalNotice() {
  return (
      <ViewWithHeader text="Mentions légales">
      <div className="px-6 pt-2 pb-12 text-sm bg-light-grey font-default">
            <h2 className="text-lg font-medium mt-6">Éditeur du site</h2>
            <p>
              Nom de l’application : <strong>VanScape</strong>
            </p>
            <p>Responsable de la publication : Chloé</p>
            <p>
              Email :{" "}
              <a href="mailto:vanscape.contact@gmail.com" className="underline">
                vanscape.contact@gmail.com
              </a>
            </p>
            <p>Adresse : disponible sur demande</p>

        <h2 className="text-lg font-medium mt-6">Hébergement</h2>
        <p>
          Hébergeur : Le site est auto-hébergé par la responsable de la
          publication
        </p>
        <p>Adresse : Hébergement personnel situé en France</p>
        <p>
          {" "}
          L'accès au site est sécurisé via HTTPS. L’hébergement est réalisé dans
          un cadre personnel, en conformité avec les exigences de sécurité et de
          protection des données.
        </p>

        <h2 className="text-lg font-medium mt-6">Propriété intellectuelle</h2>
        <p>
          Les contenus du site sont protégés par les droits d’auteur. Toute
          reproduction est interdite sans autorisation.
        </p>

        <h2 className="text-lg font-medium mt-6">Responsabilité</h2>
        <p>
          Les informations sont fournies à titre indicatif. L’éditeur ne peut
          être tenu responsable d’un usage incorrect.
        </p>

        <p className="mt-4">
          Pour en savoir plus sur la gestion de vos données personnelles,
          consultez notre{" "}
          <a
            href="/politique-confidentialite"
            className="underline text-dark-green hover:text-dark-green-hover"
          >
            politique de confidentialité
          </a>
          .
        </p>

        <p className="mt-8 text-xs text-gray-500">
          Dernière mise à jour : mai 2025
        </p>
      </div>
      </ViewWithHeader>
  );
}

export default LegalNotice;
