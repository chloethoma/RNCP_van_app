function SpotPreview ({selectedSpot, setSelectedSpot}) {
    return (
        <>
        <div className="z-30 fixed bottom-24 w-full bg-white shadow-lg rounded-xl p-4 transition-transform duration-300 ease-in-out transform translate-y-0">
        <h3 className="text-lg text-dark-grey font-default font-semibold">{selectedSpot.id}</h3>
        <p className="text-dark-grey font-default text-sm">{selectedSpot.description}</p>
        <button
          onClick={() => setSelectedSpot(null)}
          className="mt-2 px-4 py-2 bg-dark-green text-white rounded-md hover:bg-green-hover transition"
        >
          Fermer
        </button>
      </div>
        </>

    )
}

export default SpotPreview;