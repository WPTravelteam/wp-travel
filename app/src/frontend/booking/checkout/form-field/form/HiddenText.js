export default ( { names, values, index = '0', keys='2_44_55' }  ) => {

    const finaleName = names + '[' + keys.toString() + ']' + '[' + index.toString() + ']';
    return <input 
            value={ values }
            name={ finaleName }
            type='hidden'
            className='wp-travel-hidden-input-field-submited'
        />
}