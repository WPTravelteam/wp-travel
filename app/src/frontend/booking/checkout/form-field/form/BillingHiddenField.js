export default ( { names, values }  ) => {;
    return <input 
            value={ values }
            name={ names }
            type='hidden'
            className='wp-travel-hidden-input-field-submited'
        />
}