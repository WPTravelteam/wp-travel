import { Component } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { withSelect } from '@wordpress/data';

const PostList = ( props ) => {
  return(
      <>test</>
    // Post Info will be displayed here
    // props.PostList
  )
}

export default withSelect( (select, ownProps ) => {
  const { getEntityRecords } = select( 'core ');
  const postQuery = {
    per_page: -1,
    // page: 2
  }
  return {
    postList: getEntityRecords('postType', 'post', postQuery ),
  }
})(PostList)