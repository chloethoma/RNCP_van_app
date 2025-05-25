function Footer () {
  return (
    <footer className="fixed bottom-0 text-center text-xs text-grey p-2 w-full z-30">
      <p>
        ©{new Date().getFullYear()} VanScape –{" "}
        <a href="/mentions-legales" className="hover:text-grey-hover">
          Mentions légales
        </a>{" "}
        –{" "}
        <a href="/politique-confidentialite" className="hover:text-grey-hover">
          Confidentialité
        </a>
      </p>
    </footer>
  );
};

export default Footer;
