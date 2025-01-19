

function Form({children, handleAction}) {
    return (
        <div className="">
          <form onSubmit={handleAction}>
            {children}
          </form>
        </div>  
    )
}

export default Form;